<?php
namespace eMapper\Reflection\Profile\Association;

use eMapper\Manager;
use eMapper\Reflection\Profiler;
use eMapper\Query\Column;
use eMapper\Query\Attr;
use eMapper\Annotations\AnnotationsBag;
use eMapper\AssociationManager;

/**
 * The OneToOne class is an abstraction of one-to-one associations.
 * @author emaphp
 */
class OneToOne extends Association {
	public function __construct($name, AnnotationsBag $annotations, \ReflectionProperty $reflectionProperty) {
		parent::__construct('OneToOne', $name, $annotations, $reflectionProperty);
	}
	
	public function buildJoin($alias, $mainAlias) {
		$parentProfile = Profiler::getClassProfile($this->parent);
		$entityProfile = Profiler::getClassProfile($this->profile);
		
		if (isset($this->attribute)) {
			$name = $this->attribute->getArgument();
		
			if (!empty($name)) {
				//@Attr(userId)
				$property = $parentProfile->getProperty($name);
				
				if ($property === false) {
					throw new \RuntimeException(sprintf("Attribute %s not found in class %s", $name, $this->parent));
				}
				
				return sprintf('INNER JOIN @@%s %s ON %s.%s = %s.%s',
						$entityProfile->getReferredTable(), $alias,
						$alias, $property->getColumn(),
						$mainAlias, $parentProfile->getPrimaryKey(true));
			}
			else {
				//@Attr userId
				$name = $this->attribute->getValue();
				
				if (empty($name) || $name === true) {
					throw new \RuntimeException(sprintf("Association %s in class %s must define a valid attribute name", $this->name, $this->parent));
				}
				
				$property = $entityProfile->getProperty($name);
				
				if ($property === false) {
					throw new \RuntimeException(sprintf("Attribute %s not found in class %s", $name, $this->profile));
				}
				
				return sprintf('INNER JOIN @@%s %s ON %s.%s = %s.%s',
						$entityProfile->getReferredTable(), $alias,
						$mainAlias, $parentProfile->getPrimaryKey(true),
						$alias, $property->getColumn());
			}
		}
		else {
			throw new \RuntimeException(sprintf("Association %s in class must define either an attribute or a column name", $this->name, $this->parent));
		}
	}
	
	public function buildCondition($entity) {
		$parentProfile = Profiler::getClassProfile($this->parent);
		$entityProfile = Profiler::getClassProfile($this->profile);
		
		if (isset($this->attribute)) {
			$argument = $this->attribute->getArgument();
			$value = $this->attribute->getValue();
			
			if (is_null($argument)) {
				if (empty($value) || $value === true) {
					throw new \RuntimeException(sprintf("Association %s in class %s must define a valid attribute name", $this->name, $this->parent));
				}
				
				//@Attr userId
				//obtain primary key value
				$pk = $parentProfile->getProperty($parentProfile->getPrimaryKey());
				$parameter = $pk->getReflectionProperty()->getValue($entity);
				
				//build predicate
				$field = Attr::__callstatic($value);
				$predicate = $field->eq($parameter);
			}
			else {
				//@Attr(userId)
				$property = $parentProfile->getProperty($argument);
				
				if ($property === false) {
					throw new \RuntimeException(sprintf("Attribute %s not found in class %s", $argument, $this->parent));
				}
				
				$parameter = $property->getReflectionProperty()->getValue($entity);
				
				if (is_null($parameter)) {
					return false;
				}
				
				$field = Attr::__callstatic($entityProfile->getPrimaryKey());
				$predicate = $field->eq($parameter);
			}
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
		$attr = $this->attribute->getArgument();
		
		if (empty($attr)) {
			$parentProfile = Profiler::getClassProfile($this->parent);
			$entityProfile = Profiler::getClassProfile($this->profile);
			
			$pk = $parentProfile->getProperty($parentProfile->getPrimaryKey());
			$foreignKey = $pk->getReflectionProperty()->getValue($parent);
			
			$attr = $this->attribute->getValue();
			
			if (empty($attr) || $attr === false) {
				throw new \RuntimeException(sprintf("Association %s in class %s must define a valid attribute name", $this->name, $this->parent));
			}
			
			$property = $entityProfile->getProperty($attr);
			$property->getReflectionProperty()->setValue($value, $foreignKey);
		}

		return $manager->save($value, $depth);
	}
	
	public function fetchValue(Manager $manager) {		
		return $manager->get();
	}
	
	public function isForeignKey() {
		$attr = $this->attribute->getArgument();
		return !empty($attr);
	}
}
?>