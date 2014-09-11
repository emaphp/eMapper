<?php
namespace eMapper\Reflection\Profile\Association;

use eMapper\Manager;
use eMapper\Reflection\Profiler;
use eMapper\Query\Column;
use eMapper\Query\Attr;
use eMapper\Annotations\AnnotationsBag;

/**
 * The OneToMany class is an abstraction of onte-to-many associations.
 * @author emaphp
 */
class OneToMany extends Association {
	public function __construct($name, AnnotationsBag $annotations, \ReflectionProperty $reflectionProperty) {
		parent::__construct('OneToMany', $name, $annotations, $reflectionProperty);
	}
	
	public function buildJoin($alias, $mainAlias) {
		$parentProfile = Profiler::getClassProfile($this->parent);
		$entityProfile = Profiler::getClassProfile($this->profile);
		
		//obtain annotation value
		if (isset($this->attribute)) {
			$name = $this->attribute->getArgument();
			
			//if no argument try getting as a value
			if (empty($name)) {
				$name = $this->attribute->getValue();
				
				//check for a valid value
				if (empty($name) || $name === true) {
					throw new \RuntimeException(sprintf("Association %s in class %s must define a valid attribute name", $this->name, $this->parent));
				}
			}
			
			//obtain related column
			$property = $entityProfile->getProperty($name);
			
			if ($property === false) {
				throw new \RuntimeException(sprintf("Attribute %s not found in class %s", $name, $this->profile));
			}
			
			$column = $property->getColumn();
		}
		else {
			throw new \RuntimeException(sprintf("Association %s in class must define either an attribute or a column name", $this->name, $this->parent));
		}
		
		return sprintf('INNER JOIN @@%s %s ON %s.%s = %s.%s',
					   $entityProfile->getReferredTable(true), $alias,
					   $mainAlias, $parentProfile->getPrimaryKey(true),
					   $alias, $column);
	}
	
	public function buildCondition($entity) {
		$parentProfile = Profiler::getClassProfile($this->parent);

		//obtain annotation value
		if (isset($this->attribute)) {
			$name = $this->attribute->getArgument();
			
			//if no argument try getting as a value
			if (empty($name)) {
				$name = $this->attribute->getValue();
				
				if (empty($name) || $name === true) {
					throw new \RuntimeException(sprintf("Association %s in class %s must define a valid attribute name", $this->name, $this->parent));
				}
			}
			
			//obtain primary key value
			$pk = $parentProfile->getProperty($parentProfile->getPrimaryKey());
			$parameter = $pk->getReflectionProperty()->getValue($entity);
			
			//build predicate
			$field = Attr::__callstatic($name);
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
		
		if (!is_array($value)) {
			return null;
		}
		
		//get related profiles
		$parentProfile = Profiler::getClassProfile($this->parent);
		$entityProfile = Profiler::getClassProfile($this->profile);
		
		//obtains parent identifier
		$foreignKey = $this->getPropertyValue($parentProfile, $parent, $parentProfile->getPrimaryKey());
		
		//get foreign key
		$attr = $this->attribute->getValue();
			
		if (empty($attr) || $attr === false) {
			throw new \RuntimeException(sprintf("Association %s in class %s must define a valid attribute name", $this->name, $this->parent));
		}
		
		$fkProperty = $entityProfile->getProperty($attr);
		$pkProperty = $entityProfile->getProperty($entityProfile->getPrimaryKey());
		
		//create manager instance
		$manager = $mapper->buildManager($this->profile);
		$ids = [];
		
		foreach ($value as &$entity) {			
			//set foreign key before insert
			$this->setPropertyValue($entityProfile, $entity, $entityProfile->getPrimaryKey(), $foreignKey);
			
			//store object
			$ids[] = $manager->save($entity, $depth);
		}
		
		if ($fkProperty->isNullable()) {
			if (!empty($value)) {
				$query = sprintf("UPDATE %s SET %s = NULL WHERE %s NOT IN (%s)", $entityProfile->getReferredTable(), $fkProperty->getColumn(), $pkProperty->getColumn(), implode(',', $ids));
				$mapper->sql($query);
			}
		}
		else {
			if (!empty($value)) {
				$query = sprintf("DELETE FROM %s WHERE %s = %s AND %s NOT IN (%s)", $entityProfile->getReferredTable(), $fkProperty->getColumn(), $foreignKey, $pkProperty->getColumn(), implode(',', $ids));
				//clean related values
				$mapper->sql($query);
			}
			else {
				//delete all
				$query = sprintf("DELETE FROM %s WHERE %s = %s", $entityProfile->getReferredTable(), $fkProperty->getColumn(), $foreignKey);
			}	
		}
		
		return null;
	}
	
	public function fetchValue(Manager $manager) {
		return $manager->find();
	}
}
?>
