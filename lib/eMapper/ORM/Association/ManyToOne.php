<?php
namespace eMapper\ORM\Association;

use eMapper\Fluent\Query\AbstractQuery;
use eMapper\ORM\AssociationManager;
use eMapper\Mapper;
use eMapper\Query\Column;
use eMapper\Reflection\Profiler;

/**
 * The ManyToOne class is an abstraction of many-to-one associations.
 * @author emaphp
 */
class ManyToOne extends Association {
	public function __construct($propertyName, $reflectionProperty, $propertyAnnotations) {
		parent::__construct('ManyToOne', $propertyName, $reflectionProperty, $propertyAnnotations);
	}
	
	public function getJoinPredicate($entity) {
		//get attribute name
		if (empty($this->attribute))
			throw new \RuntimeException(sprintf("Many-to-one association '%s' in class '%s' must define an attribute through the @Attr annotation", $this->name, $this->parentClass));
		
		$attr = $this->attribute->getArgument();
		if (empty($attr))
			throw new \RuntimeException(sprintf("Many-to-one association '%s' in class '%s' must define a valid attribute name", $this->name, $this->parentClass));
		
		$parentProfile = Profiler::getClassProfile($this->parentClass);
		$entityProfile = Profiler::getClassProfile($this->entityClass);
		
		//validate attribute
		if (!$parentProfile->hasProperty($attr))
			throw new \RuntimeException(sprintf("Attribute '%s' not found in class '%s'", $attr, $this->parentClass));
		
		//get foreign key value
		$parameter = $this->getPropertyValue($parentProfile, $entity, $attr);
		
		//build predicate
		return Column::__callstatic($entityProfile->getPrimaryKey(true))->eq($parameter);
	}
	
	public function appendJoin(AbstractQuery &$query, $sourceAlias, $targetAlias, $left_join = false) {
		//get attribute name
		if (empty($this->attribute))
			throw new \RuntimeException(sprintf("Many-to-one association '%s' in class '%s' must define an attribute through the @Attr annotation", $this->name, $this->parentClass));
		
		$attr = $this->attribute->getArgument();
		if (empty($attr))
			throw new \RuntimeException(sprintf("Many-to-one association '%s' in class '%s' must define a valid attribute name", $this->name, $this->parentClass));
		
		$parentProfile = Profiler::getClassProfile($this->parentClass);
		$entityProfile = Profiler::getClassProfile($this->entityClass);
		
		//validate attribute
		if (!$parentProfile->hasProperty($attr))
			throw new \RuntimeException(sprintf("Attribute '%s' not found in class '%s'", $attr, $this->parentClass));
		
		//build condition
		$cond = sprintf(
			'%s.%s = %s.%s',
			$sourceAlias, $parentProfile->getProperty($attr)->getColumn(),
			$targetAlias, $entityProfile->getPrimaryKey(true)
		);
		
		if ($left_join)
			$query->leftJoin($entityProfile->getEntityTable(), $targetAlias, $cond);
		else
			$query->innerJoin($entityProfile->getEntityTable(), $targetAlias, $cond);
	}
	
	public function appendContextJoin(AbstractQuery &$query, $mainAlias, $contextAlias) {
		//get attribute name
		if (empty($this->attribute))
			throw new \RuntimeException(sprintf("Many-to-one association '%s' in class '%s' must define an attribute through the @Attr annotation", $this->name, $this->parentClass));
		
		$attr = $this->attribute->getArgument();
		if (empty($attr))
			throw new \RuntimeException(sprintf("Many-to-one association '%s' in class '%s' must define a valid attribute name", $this->name, $this->parentClass));
		
		$parentProfile = Profiler::getClassProfile($this->parentClass);
		$entityProfile = Profiler::getClassProfile($this->entityClass);
		
		//validate attribute
		if (!$parentProfile->hasProperty($attr))
			throw new \RuntimeException(sprintf("Attribute '%s' not found in class '%s'", $attr, $this->parentClass));

		//build condition
		$cond = sprintf(
			'%s.%s = %s.%s',
			$mainAlias, $entityProfile->getPrimaryKey(true),
			$contextAlias, $parentProfile->getProperty($attr)->getColumn()
		);
		
		//append join
		$query->innerJoin($parentProfile->getEntityTable(), $contextAlias, $cond);
	}
	
	public function fetchValue(AssociationManager $manager) {
		return $manager->get();
	}
	
	public function save(Mapper $mapper, $parent, $value, $depth) {
		if ($value instanceof AssociationManager)
			return null;
	
		$manager = $mapper->newManager($this->entityClass);
		return $manager->save($value, $depth);
	}
	
	public function delete(Mapper $mapper, $foreignKey) {
		//
	}
}