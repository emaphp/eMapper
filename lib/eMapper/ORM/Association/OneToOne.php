<?php
namespace eMapper\ORM\Association;

use eMapper\Fluent\Query\AbstractQuery;
use eMapper\ORM\AssociationManager;
use eMapper\Query\Column;
use eMapper\Query\Attr;
use eMapper\Reflection\Profiler;
use eMapper\Mapper;

/**
 * The OneToOne class is an abstraction of one-to-one associations.
 * @author emaphp
 */
class OneToOne extends Association {
	public function __construct($propertyName, $reflectionProperty, $propertyAnnotations) {
		parent::__construct('OneToOne', $propertyName, $reflectionProperty, $propertyAnnotations);
	}
	
	public function getJoinPredicate($entity) {
		//get attribute name
		if (!isset($this->attribute))
			throw new \RuntimeException(sprintf("One-to-one association '%s' in class '%s' must define an attribute through the @Attr annotation", $this->name, $this->parentClass));
		
		$parentProfile = Profiler::getClassProfile($this->parentClass);
		$entityProfile = Profiler::getClassProfile($this->entityClass);
		
		$attr = $this->attribute->getArgument();
		
		//determine where is attribute
		if (empty($attr)) { //@Attr userId -> check entity
			$attr = $this->attribute->getValue();
			
			//validate attribute name
			if (empty($attr) || !is_string($attr))
				throw new \RuntimeException(sprintf("One-to-one association '%s' in class '%s' must define a valid attribute name", $this->name, $this->parentClass));
			
			if (!$entityProfile->hasProperty($attr))
				throw new \RuntimeException(sprintf("Attribute '%s' not found in class '%s'", $attr, $this->entityClass));
			
			//obtain primary key value
			$parameter = $this->getPropertyValue($parentProfile, $entity, $parentProfile->getPrimaryKey());
			
			//build predicate
			$column = $entityProfile->getProperty($attr)->getColumn();
			$predicate = Column::__callstatic($column)->eq($parameter);
		}
		else { //@Attr(userId) -> check parent
			if (!$parentProfile->hasProperty($attr))
				throw new \RuntimeException(sprintf("Attribute '%s' not found in class '%s'", $attr, $this->parentClass));
			
			//get foreign key value
			$parameter = $this->getPropertyValue($parentProfile, $entity, $attr);
			
			//build predicate
			$predicate = Column::__callstatic($entityProfile->getPrimaryKey(true))->eq($parameter);
		}
		
		return $predicate;
	}
	
	public function appendJoin(AbstractQuery &$query, $sourceAlias, $targetAlias, $left_join = false) {
		//get attribute name
		if (!isset($this->attribute))
			throw new \RuntimeException(sprintf("One-to-one association '%s' in class '%s' must define an attribute name through the @Attr annotation", $this->name, $this->parentClass));
		
		//get related class profiles
		$parentProfile = Profiler::getClassProfile($this->parentClass);
		$entityProfile = Profiler::getClassProfile($this->entityClass);
		
		$attr = $this->attribute->getArgument();
		
		if (empty($attr)) { //@Attr userId
			$attr = $this->attribute->getValue();
			
			if (empty($attr) || !is_string($attr))
				throw new \RuntimeException(sprintf("One-to-one association '%s' in class '%s' must define a valid attribute name", $this->name, $this->parentClass));
				
			if (!$entityProfile->hasProperty($attr))
				throw new \RuntimeException(sprintf("Attribute '%s' not found in class '%s'", $attr, $this->entityClass));
			
			//build join condition
			$cond = sprintf(
				'%s.%s = %s.%s',
				$sourceAlias, $parentProfile->getPrimaryKey(true),
				$targetAlias, $entityProfile->getProperty($attr)->getColumn()
			);
			
			//add join
			if ($left_join)
				$query->leftJoin($entityProfile->getEntityTable(), $targetAlias, $cond);
			else
				$query->innerJoin($entityProfile->getEntityTable(), $targetAlias, $cond);
		}
		else { //@Attr(userId)
			if (!$parentProfile->hasProperty($attr))
				throw new \RuntimeException(sprintf("Attribute '%s' not found in class %s", $attr, $this->parentClass));
			
			//build join condition
			$cond = sprintf(
				'%s.%s = %s.%s',
				$sourceAlias, $parentProfile->getProperty($attr)->getColumn(),
				$targetAlias, $entityProfile->getPrimaryKey(true)
			);
			
			//add join
			if ($left_join)
				$query->leftJoin($entityProfile->getEntityTable(), $targetAlias, $cond);
			else
				$query->innerJoin($entityProfile->getEntityTable(), $targetAlias, $cond);
		}
	}
	
	public function appendContextJoin(AbstractQuery &$query, $mainAlias, $contextAlias) {
		//get attribute name
		if (!isset($this->attribute))
			throw new \RuntimeException(sprintf("One-to-one association '%s' in class '%s' must define an attribute name through the @Attr annotation", $this->name, $this->parentClass));
		
		$parentProfile = Profiler::getClassProfile($this->parentClass);
		$entityProfile = Profiler::getClassProfile($this->entityClass);
		
		$attr = $this->attribute->getArgument();
		if (empty($attr)) { //@Attr userId -> check entity
			//validate attribute name
			$attr = $this->attribute->getValue();
			
			if (empty($attr) || !is_string($attr))
				throw new \RuntimeException(sprintf("One-to-one association '%s' in class '%s' must define a valid attribute name", $this->name, $this->parentClass));
			
			if (!$entityProfile->hasProperty($attr))
				throw new \RuntimeException(sprintf("Attribute '%s' not found in class '%s'", $attr, $this->parentClass));
			
			//build join condition
			$cond = sprintf(
				'%s.%s = %s.%s',
				$mainAlias, $entityProfile->getProperty($attr)->getColumn(),
				$contextAlias, $parentProfile->getPrimaryKey(true)
			);
			
			//append join
			$query->innerJoin($parentProfile->getEntityTable(), $contextAlias, $cond);
		}
		else { //@Attr(userId)
			if (!$parentProfile->hasProperty($attr))
				throw new \RuntimeException(sprintf("Attribute '%s' not found in class %s", $attr, $this->parentClass));
				
			//build join condition
			$cond = sprintf(
				'%s.%s = %s.%s',
				$mainAlias, $entityProfile->getPrimaryKey(true),
				$contextAlias, $parentProfile->getProperty($attr)->getColumn()
			);
				
			//append join
			$query->innerJoin($parentProfile->getEntityTable(), $contextAlias, $cond);
		}
	}
	
	public function fetchValue(AssociationManager $manager) {
		return $manager->get();
	}
	
	public function save(Mapper $mapper, $parent, $value, $depth) {
		if ($value instanceof AssociationManager)
			return null;
		
		//get attribute name
		if (!isset($this->attribute))
			throw new \RuntimeException(sprintf("One-to-one association '%s' in class '%s' must define an attribute name through the @Attr annotation", $this->name, $this->parentClass));
		
		$manager = $mapper->newManager($this->entityClass);
		$attr = $this->attribute->getArgument();
		
		if (empty($attr)) { //Attr userId
			$attr = $this->attribute->getValue();
			if (empty($attr) || !is_string($attr))
				throw new \RuntimeException(sprintf("One-to-one association '%s' in class '%s' must define a valid attribute name", $this->name, $this->parentClass));
			if (!$entityProfile->hasProperty($attr))
				throw new \RuntimeException(sprintf("Attribute %s not found in class %s", $attr, $this->entityClass));
			
			$parentProfile = Profiler::getClassProfile($this->parentClass);
			$entityProfile = Profiler::getClassProfile($this->entityClass);
			
			//sett foreign key value
			$foreignKey = $this->getPropertyValue($parentProfile, $parent, $parentProfile->getPrimaryKey());
			$this->setPropertyValue($entityProfile, $value, $attr, $foreignKey);
		}
		
		return $manager->save($value, $depth);
	}
	
	public function delete(Mapper $mapper, $foreignKey) {
		//get attribute name
		if (!isset($this->attribute))
			throw new \RuntimeException(sprintf("One-to-one association '%s' in class '%s' must define an attribute name through the @Attr annotation", $this->name, $this->parentClass));
		
		$attr = $this->attribute->getValue();
		if (empty($attr) || !is_string($attr))
			throw new \RuntimeException(sprintf("One-to-one association '%s' in class '%s' must define a valid attribute name", $this->name, $this->parentClass));
		
		$entityProfile = Profiler::getClassProfile($this->entityClass);
		
		//validate attribute
		if (!$entityProfile->hasProperty($attr))
			throw new \RuntimeException(sprintf("Attribute '%s' not found in class '%s'", $attr, $this->entityClass));
		
		//check if property is nullable
		$property = $entityProfile->getProperty($attr);
		
		if ($property->isNullable()) {
			//update foreign key
			$query = $mapper->newQuery()
			->update($entityProfile->getEntityTable())
			->set($column, null)
			->where(Column::__callstatic($property->getColumn())->eq($foreignKey));
			
			$query->exec();
		}
		
		//delete related
		$manager = $mapper->newManager($this->entityClass);
		$related = $manager->get(Attr::__callstatic($attr)->eq($foreignKey));
		
		if (!is_null($related))
			$manager->delete($related);
	}
	
	public function isForeignKey() {
		$attr = $this->attribute->getArgument();
		return !empty($attr);
	}
}