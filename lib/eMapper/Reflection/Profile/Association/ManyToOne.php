<?php
namespace eMapper\Reflection\Profile\Association;

use eMapper\Manager;
use eMapper\Reflection\Profiler;
use eMapper\Query\Column;
use eMapper\Query\Attr;

/**
 * The ManyToOne class is an abstraction of many-to-one associations.
 * @author emaphp
 */
class ManyToOne extends AbstractAssociation {
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
			
			$property = $parentProfile->getProperty($name);
			
			if ($property === false) {
				throw new \RuntimeException(sprintf("Attribute %s not found in class %s", $name, $this->parent));
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
					   $entityProfile->getReferredTable(), $alias,
					   $mainAlias, $column,
					   $alias, $entityProfile->getPrimaryKey(true));
	}
	
	public function buildCondition($entity) {
		$parentProfile = Profiler::getClassProfile($this->parent);
		$entityProfile = Profiler::getClassProfile($this->profile);
		
		if (isset($this->column)) {
			$name = $this->column->getArgument();
			
			if (empty($name)) {
				$name = $this->column->getValue();
				
				if (empty($name) || $name === true) {
					throw new \RuntimeException(sprintf("Association %s in class %s must define a valid column name", $this->name, $this->parent));
				}
			}
			
			$property = $parentProfile->getPropertyByColumn($name);
			
			if ($property === false) {
				throw new \RuntimeException(sprintf("No attribute found for column %s in class %s", $name, $this->parent));
			}
			
			$parameter = $property->getReflectionProperty()->getValue($entity);
			
			if (is_null($parameter)) {
				return false;
			}
			
			//build predicate
			$field = Column::__callstatic($entityProfile->getPrimaryKey(true));
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
			
			$property = $parentProfile->getProperty($name);
			
			if ($property === false) {
				throw new \RuntimeException(sprintf("Attribute %s not found in class %s", $name, $this->parent));
			}
			
			$parameter = $property->getReflectionProperty()->getValue($entity);
			
			if (is_null($parameter)) {
				return false;
			}
			
			$field = Attr::__callstatic($entityProfile->getPrimaryKey());
			$predicate = $field->eq($parameter);
		}
		else {
			throw new \RuntimeException(sprintf("Association %s in class must define either an attribute or a column name", $this->name, $this->parent));
		}
		
		return $predicate;
	}
	
	public function fetchValue(Manager $manager) {
		return $manager->get();
	}
}
?>