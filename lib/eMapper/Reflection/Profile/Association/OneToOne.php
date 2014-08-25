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
			$property = $this->attribute->getValue();
		
			if (empty($property)) {
				//@Attr(userId)
				$property = $parentProfile->getProperty($this->attribute->getArgument());
				
				return sprintf('INNER JOIN @@%s %s ON %s.%s = %s.%s',
						$parentProfile->getReferredTable(), $alias,
						$alias, $entityProfile->getPrimaryKey(true),
						$mainAlias, $property->getColumn());
			}
			else {
				//@Attr userId
				$property = $entityProfile->getProperty($property);
				
				return sprintf('INNER JOIN @@%s %s ON %s.%s = %s.%s',
						$parentProfile->getReferredTable(true), $alias,
						$mainAlias, $property->getColumn(),
						$alias, $parentProfile->getPrimaryKey(true));
			}
		
			$property = $entityProfile->getProperty($property);
			$column = $property->getColumn();
		}
		elseif (isset($this->column)) {
			$column = $this->column->getValue();
		
			if (empty($column)) {
				//@Column(user_id)
				$column = $this->column->getArgument();
				return sprintf('INNER JOIN @@%s %s ON %s.%s = %s.%s',
						   $parentProfile->getReferredTable(), $alias,
						   $alias, $entityProfile->getPrimaryKey(true),
						   $mainAlias, $column);
			}
			else {
				//@Column user_id
				return sprintf('INNER JOIN @@%s %s ON %s.%s = %s.%s',
						$parentProfile->getReferredTable(true), $alias,
						$mainAlias, $column,
						$alias, $parentProfile->getPrimaryKey(true));
			}
		}
	}
	
	public function buildCondition($entity) {
		$parentProfile = Profiler::getClassProfile($this->parent);
		$entityProfile = Profiler::getClassProfile($this->profile);
		
		if (isset($this->column)) {
			$argument = $this->column->getArgument();
			$value = $this->column->getValue();
				
			if (is_null($argument)) {
				//@Column user_id
				$pk = $parentProfile->getPropertyByColumn($parentProfile->getPrimaryKey(true));
				$parameter = $pk->getReflectionProperty()->getValue($entity);
				
				$field = Column::__callstatic($value);
				$predicate = $field->eq($parameter);
			}
			else {
				//@Column(user_id)
				$property = $parentProfile->getPropertyByColumn($argument);
				$parameter = $property->getReflectionProperty()->getValue($entity);
				
				$field = Column::__callstatic($entityProfile->getPrimaryKey(true));
				$predicate = $field->eq($arg);
			}
		}
		elseif (isset($this->attribute)) {
			$argument = $this->attribute->getArgument();
			$value = $this->attribute->getValue();
			
			if (is_null($argument)) {
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
				$parameter = $property->getReflectionProperty()->getValue($entity);
				
				$field = Attr::__callstatic($entityProfile->getPrimaryKey());
				$predicate = $field->eq($parameter);
			}
		}
		
		return $predicate;
	}
	
	public function fetchValue(Manager $manager) {		
		return $manager->get();
	}
}
?>