<?php
namespace eMapper\Reflection\Profile\Association;

use eMapper\Manager;
use eMapper\Reflection\Profiler;
use eMapper\Query\Column;
use eMapper\Query\Attr;

/**
 * The OneToOne class is an abstraction of one-to-one associations.
 * @author emaphp
 */
class OneToOne extends AbstractAssociation {
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
		elseif (isset($this->column)) {
			$column = $this->column->getArgument();
			
			if (!empty($column)) {
				//@Column(user_id)
				return sprintf('INNER JOIN @@%s %s ON %s.%s = %s.%s',
						   $entityProfile->getReferredTable(), $alias,
						   $alias, $entityProfile->getPrimaryKey(true),
						   $mainAlias, $column);
			}
			else {
				$column = $this->column->getValue();
				
				if (empty($column) || $column === true) {
					throw new \RuntimeException(sprintf("Association %s in class %s must define a valid column name", $this->name, $this->parent));
				}
				
				//@Column user_id
				return sprintf('INNER JOIN @@%s %s ON %s.%s = %s.%s',
						$entityProfile->getReferredTable(), $alias,
						$mainAlias, $parentProfile->getPrimaryKey(true),
						$alias, $column);
			}
		}
		else {
			throw new \RuntimeException(sprintf("Association %s in class must define either an attribute or a column name", $this->name, $this->parent));
		}
	}
	
	public function buildCondition($entity) {
		$parentProfile = Profiler::getClassProfile($this->parent);
		$entityProfile = Profiler::getClassProfile($this->profile);
		
		if (isset($this->column)) {
			$argument = $this->column->getArgument();
			$value = $this->column->getValue();
				
			if (is_null($argument)) {
				if (empty($value) || $value === true) {
					throw new \RuntimeException(sprintf("Association %s in class %s must define a valid column name", $this->name, $this->parent));
				}
				
				//@Column user_id
				$pk = $parentProfile->getPropertyByColumn($parentProfile->getPrimaryKey(true));
				$parameter = $pk->getReflectionProperty()->getValue($entity);

				$field = Column::__callstatic($value);
				$predicate = $field->eq($parameter);
			}
			else {
				//@Column(user_id)
				$property = $parentProfile->getPropertyByColumn($argument);
				
				if ($property === false) {
					throw new \RuntimeException(sprintf("No attribute found for column %s in class %s", $argument, $this->parent));
				}
				
				$parameter = $property->getReflectionProperty()->getValue($entity);
				
				if (is_null($parameter)) {
					return false;
				}
				
				$field = Column::__callstatic($entityProfile->getPrimaryKey(true));
				$predicate = $field->eq($parameter);
			}
		}
		elseif (isset($this->attribute)) {
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
	
	public function fetchValue(Manager $manager) {		
		return $manager->get();
	}
}
?>