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
			$property = $this->attribute->getArgument();
			
			if (empty($property)) {
				$property = $this->attribute->getValue();
			}
			
			$property = $parentProfile->getProperty($property);
			$column = $property->getColumn();
		}
		elseif (isset($this->column)) {
			$column = $this->column->getArgument();
			
			if (empty($column)) {
				$column = $this->column->getValue();
			}
		}
		
		return sprintf('INNER JOIN @@%s %s ON %s.%s = %s.%s',
					   $parentProfile->getReferredTable(), $alias,
					   $alias, $column,
					   $mainAlias, $entityProfile->getPrimaryKey(true));
	}
	
	public function buildCondition($entity) {
		$parentProfile = Profiler::getClassProfile($this->parent);
		$entityProfile = Profiler::getClassProfile($this->profile);
		
		if (isset($this->column)) {
			$value = $this->column->getArgument();
			
			if (empty($value)) {
				$value = $this->column->getValue();
			}
			
			$property = $parentProfile->getPropertyByColumn($value);
			$parameter = $property->getReflectionProperty()->getValue($entity);
			
			//build predicate
			$field = Column::__callstatic($entityProfile->getPrimaryKey(true));
			$predicate = $field->eq($parameter);
		}
		elseif (isset($this->attribute)) {
			$value = $this->attribute->getArgument();
			
			if (empty($value)) {
				$value = $this->attribute->getValue();
			}
			
			$property = $parentProfile->getProperty($value);
			$parameter = $property->getReflectionProperty()->getValue($entity);
			
			$field = Attr::__callstatic($entityProfile->getPrimaryKey());
			$predicate = $field->eq($parameter);
		}
		
		return $predicate;
	}
	
	public function fetchValue(Manager $manager) {
		return $manager->get();
	}
}
?>