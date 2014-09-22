<?php
namespace eMapper\Reflection\Profile\Association;

use eMapper\Manager;
use eMapper\Reflection\Profiler;
use eMapper\Query\Column;
use eMapper\Query\Attr;
use eMapper\AssociationManager;
use eMapper\Annotations\AnnotationsBag;

/**
 * The ManyToOne class is an abstraction of many-to-one associations.
 * @author emaphp
 */
class ManyToOne extends Association {
	public function __construct($name, AnnotationsBag $annotations, \ReflectionProperty $reflectionProperty) {
		parent::__construct('ManyToOne', $name, $annotations, $reflectionProperty);
	}
	
	public function buildJoin($alias, $mainAlias, $joinType) {
		//get related profiles
		$parentProfile = Profiler::getClassProfile($this->parent);
		$entityProfile = Profiler::getClassProfile($this->profile);
		
		if (isset($this->attribute)) {
			//get attribute name
			$name = $this->attribute->getArgument();
			
			//try getting attribute as value instead
			if (empty($name)) {
				$name = $this->attribute->getValue();
				
				if (empty($name) || $name === true) {
					throw new \RuntimeException(sprintf("Association %s in class %s must define a valid attribute name", $this->name, $this->parent));
				}
			}
			
			//obtain column name
			$property = $parentProfile->getProperty($name);
			
			if ($property === false) {
				throw new \RuntimeException(sprintf("Attribute %s not found in class %s", $name, $this->parent));
			}
			
			$column = $property->getColumn();
		}
		else {
			throw new \RuntimeException(sprintf("Association %s in class must define either an attribute or a column name", $this->name, $this->parent));
		}
		
		return sprintf('%s JOIN @@%s %s ON %s.%s = %s.%s',
					   $joinType,
					   $entityProfile->getReferredTable(), $alias,
					   $mainAlias, $column,
					   $alias, $entityProfile->getPrimaryKey(true));
	}
	
	public function buildCondition($entity) {
		//get related profiles
		$parentProfile = Profiler::getClassProfile($this->parent);
		$entityProfile = Profiler::getClassProfile($this->profile);
		
		if (isset($this->attribute)) {
			//get attribute name
			$name = $this->attribute->getArgument();
			
			//get name as value instead
			if (empty($name)) {
				$name = $this->attribute->getValue();
				
				if (empty($name) || $name === true) {
					throw new \RuntimeException(sprintf("Association %s in class %s must define a valid attribute name", $this->name, $this->parent));
				}
			}
			
			//obtain attribute value
			$property = $parentProfile->getProperty($name);
			
			if ($property === false) {
				throw new \RuntimeException(sprintf("Attribute %s not found in class %s", $name, $this->parent));
			}
			
			$parameter = $property->getReflectionProperty()->getValue($entity);
			
			if (is_null($parameter)) {
				return false;
			}
			
			//build predicate
			$field = Attr::__callstatic($entityProfile->getPrimaryKey());
			$predicate = $field->eq($parameter);
		}
		else {
			throw new \RuntimeException(sprintf("Association %s in class must define either an attribute or a column name", $this->name, $this->parent));
		}
		
		return $predicate;
	}
	
	public function save($mapper, $parent, $value, $depth) {
		if ($value instanceof AssociationManager) {
			return null;
		}
		
		$manager = $mapper->buildManager($this->profile);
		return $manager->save($value, $depth);
	}
	
	public function delete($mapper, $foreignKey) {
		//
	}
	
	public function fetchValue(Manager $manager) {
		return $manager->get();
	}
}
?>