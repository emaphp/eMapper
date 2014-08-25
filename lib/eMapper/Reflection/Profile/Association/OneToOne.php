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
			$property = $this->attribute->getArgument();
		
			if (!empty($property)) {
				//@Attr(userId)
				$property = $parentProfile->getProperty($property);
				
				return sprintf('INNER JOIN @@%s %s ON %s.%s = %s.%s',
						$entityProfile->getReferredTable(), $alias,
						$alias, $property->getColumn(),
						$mainAlias, $parentProfile->getPrimaryKey(true));
			}
			else {
				//@Attr userId
				$property = $this->attribute->getValue();
				$property = $entityProfile->getProperty($property);
				
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
				
				//@Column user_id
				return sprintf('INNER JOIN @@%s %s ON %s.%s = %s.%s',
						$entityProfile->getReferredTable(), $alias,
						$mainAlias, $parentProfile->getPrimaryKey(true),
						$alias, $column);
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
				$predicate = $field->eq($parameter);
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