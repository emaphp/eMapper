<?php
namespace eMapper\ORM\Association;

use eMapper\Fluent\Query\AbstractQuery;
use eMapper\ORM\AssociationManager;
use eMapper\Reflection\Profiler;
use eMapper\Query\Attr;
use eMapper\Query\Column;
use eMapper\Mapper;

/**
 * The OneToMany class is an abstraction of one-to-many associations.
 * @author emaphp
 */
class OneToMany extends Association {
	public function getJoinPredicate($entity) {
		//obtain attribute name
		if (!isset($this->attribute))
			throw new \RuntimeException(sprintf("One-to-many association '%s' in class '%s' must define an attribute through the @Attr annotation", $this->name, $this->parentClass));
		
		$attr = $this->attribute->getValue();		
		if (empty($attr) || !is_string($attr))
			throw new \RuntimeException(sprintf("One-to-many association '%s' in class '%s' must define a valid attribute name", $this->name, $this->parentClass));
		
		$parentProfile = Profiler::getClassProfile($this->parentClass);
		$entityProfile = Profiler::getClassProfile($this->entityClass);
		
		//validate attribute
		if (!$entityProfile->hasProperty($attr))
			throw new \RuntimeException(sprintf("Attribute '%s' not found in class '%s'", $attr, $this->entityClass));
		
		//obtain declaring class primary key value
		$parameter = $this->getPropertyValue($parentProfile, $entity, $parentProfile->getPrimaryKey());
		
		//build predicate
		$column = $entityProfile->getProperty($attr)->getColumn();
		return Column::__callstatic($column)->eq($parameter);
	}
	
	public function appendJoin(AbstractQuery &$query, $mainAlias, $contextAlias) {
		//obtain attribute name
		if (!isset($this->attribute))
			throw new \RuntimeException(sprintf("One-to-many association '%s' in class '%s' must define an attribute through the @Attr annotation", $this->name, $this->parentClass));
		
		$attr = $this->attribute->getValue();		
		if (empty($attr) || !is_string($attr))
			throw new \RuntimeException(sprintf("One-to-many association '%s' in class '%s' must define a valid attribute name", $this->name, $this->parentClass));
		
		$parentProfile = Profiler::getClassProfile($this->parentClass);
		$entityProfile = Profiler::getClassProfile($this->entityClass);
		
		//build join condition
		$cond = sprintf(
			'%s.%s = %s.%s',
			$mainAlias, $entityProfile->getProperty($attr)->getColumn(),
			$contextAlias, $parentProfile->getPrimaryKey(true)
		);
		
		//append join
		$query->innerJoin($parentProfile->getEntityTable(), $contextAlias, $cond);
	}
	
	public function fetchValue(AssociationManager $manager) {
		return $manager->find();
	}
	
	public function save(Mapper $mapper, $parent, $value, $depth) {
		//if not array then return
		if ($value instanceof AssociationManager || !is_array($value))
			return null;
		
		//obtain attribute name
		if (!isset($this->attribute))
			throw new \RuntimeException(sprintf("One-to-many association '%s' in class '%s' must define an attribute through the @Attr annotation", $this->name, $this->parentClass));
		
		$attr = $this->attribute->getValue();		
		if (empty($attr) || !is_string($attr))
			throw new \RuntimeException(sprintf("One-to-many association '%s' in class '%s' must define a valid attribute name", $this->name, $this->parentClass));
		
		$parentProfile = Profiler::getClassProfile($this->parentClass);
		$entityProfile = Profiler::getClassProfile($this->entityClass);
		
		//validate attribute
		if (!$entityProfile->hasProperty($attr))
			throw new \RuntimeException(sprintf("Attribute '%s' not found in class '%s'", $attr, $this->entityClass));
		
		//obtain primey key value from parent
		$foreignKey = $this->getPropertyValue($parentProfile, $parent, $parentProfile->getPrimaryKey());
		$manager = $mapper->newManager($this->entityClass);
		$ids = [];
		
		foreach ($value as &$entity) {
			//set foreign key before insert
			$this->setPropertyValue($entityProfile, $entity, $attr, $foreignKey);
				
			//store object
			$ids[] = $manager->save($entity, $depth);
		}
		
		//check if property is nullable
		$property = $entityProfile->getProperty($attr);
		
		if ($property->isNullable()) {
			//update foreign key
			$column = $property->getColumn();
			
			$query = $mapper->newQuery()
			->update($entityProfile->getEntityTable())
			->set($column, null);
			
			if (!empty($value))
				$query->where(Column::__callstatic($entityProfile->getPrimaryKey(true))->in($ids, false));
			else
				$query->where(Column::__callstatic($column)->eq($foreignKey));
			
			$query->exec();
		}
		
		//delete not related
		if (!empty($value)) {
			$unrelated = $manager
			->filter(Attr::__callstatic($attr)->eq($foreignKey), Attr::__callstatic($entityProfile->getPrimaryKey())->in($ids, false))
			->find();
			
			foreach ($unrelated as $entity)
				$manager->delete($entity);
		}
		else {
			$unrelated = $manager->find(Attr::__callstatic($attr)->eq($foreignKey));
			
			foreach ($unrelated as $entity)
				$manager->delete($entity);
		}
	}
	
	public function delete(Mapper $mapper, $foreignKey) {
		//obtain attribute name
		if (!isset($this->attribute))
			throw new \RuntimeException(sprintf("One-to-many association '%s' in class '%s' must define an attribute through the @Attr annotation", $this->name, $this->parentClass));
		
		$attr = $this->attribute->getValue();		
		if (empty($attr) || !is_string($attr))
			throw new \RuntimeException(sprintf("One-to-many association '%s' in class '%s' must define a valid attribute name", $this->name, $this->parentClass));
		
		$entityProfile = Profiler::getClassProfile($this->entityClass);
		if (!$entityProfile->hasProperty($attr))
			throw new \RuntimeException(sprintf("Attribute '%s' not found in class '%s'", $attr, $this->entityClass));
		
		//check if property is nullable
		$property = $entityProfile->getProperty($attr);
		
		if ($property->isNullable()) {
			$column = $property->getColumn();
				
			//set foreign key to NULL
			$query = $mapper->newQuery()
			->update($entityProfile->getEntityTable())
			->set($column, null)
			->where(Column::__callstatic($column)->eq($foreignKey));
			
			$query->exec();
		}
		
		//delete related
		$manager = $mapper->newManager($this->entityClass);
		$related = $manager->find(Attr::__callstatic($attr)->eq($foreignKey));
				
		foreach ($related as $value)
			$manager->delete($value);
	}
}