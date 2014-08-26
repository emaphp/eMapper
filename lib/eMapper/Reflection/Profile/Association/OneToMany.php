<?php
namespace eMapper\Reflection\Profile\Association;

use eMapper\Manager;
use eMapper\Reflection\Profiler;
use eMapper\Query\Column;
use eMapper\Query\Attr;

/**
 * The OneToMany class is an abstraction of onte-to-many associations.
 * @author emaphp
 */
class OneToMany extends AbstractAssociation {
	public function buildJoin($alias, $mainAlias) {
		$parentProfile = Profiler::getClassProfile($this->parent);
		$entityProfile = Profiler::getClassProfile($this->profile);
		
		if (isset($this->attribute)) {
			$name = $this->attribute->getArgument();
			
			if (empty($name)) {
				$name = $this->attribute->getValue();
				
				if (empty($name) || $name === true) {
					throw new \RuntimeException(sprintf("Association %s in class %s must define a valid attribute name", $this->name, $this->parent));
				}
			}
				
			$property = $entityProfile->getProperty($name);
			
			if ($property === false) {
				throw new \RuntimeException(sprintf("Attribute %s not found in class %s", $name, $this->profile));
			}
			
			$column = $property->getColumn();
		}
		elseif (isset($this->column)) {
			$column = $this->column->getArgument();
			
			if (empty($column)) {
				$column = $this->column->getValue();
				
				if (empty($column) || $column === true) {
					throw new \RuntimeException(sprintf("Association %s in class %s must define a valid column name", $this->name, $this->parent));
				}
			}
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
		
		if (isset($this->column)) {
			$name = $this->column->getArgument();
			
			if (empty($name)) {
				$name = $this->column->getValue();
				
				if (empty($name) || $name === true) {
					throw new \RuntimeException(sprintf("Association %s in class %s must define a valid column name", $this->name, $this->parent));
				}
			}
				
			$pk = $parentProfile->getPropertyByColumn($parentProfile->getPrimaryKey(true));
			$parameter = $pk->getReflectionProperty()->getValue($entity);
				
			//build predicate
			$field = Column::__callstatic($name);
			$predicate = $field->eq($parameter);
		}
		elseif (isset($this->attribute)) {
			$name = $this->attribute->getArgument();
			
			if (empty($name)) {
				$name = $this->attribute->getValue();
				
				if (empty($name) || $name === true) {
					throw new \RuntimeException(sprintf("Association %s in class %s must define a valid attribute name", $this->name, $this->parent));
				}
			}
				
			$pk = $parentProfile->getProperty($parentProfile->getPrimaryKey());
			$parameter = $pk->getReflectionProperty()->getValue($entity);
				
			$field = Attr::__callstatic($name);
			$predicate = $field->eq($parameter);
		}
		else {
			throw new \RuntimeException(sprintf("Association %s in class must define either an attribute or a column name", $this->name, $this->parent));
		}
		
		return $predicate;
	}
	
	public function fetchValue(Manager $manager) {
		return $manager->find();
	}
}
?>