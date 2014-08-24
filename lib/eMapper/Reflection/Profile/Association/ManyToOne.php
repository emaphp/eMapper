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
			$property = $this->attribute->getValue();
			
			if (empty($property)) {
				$property = $this->attribute->getArgument();
			}
			
			$property = $parentProfile->getProperty($property);
			$column = $property->getColumn();
		}
		elseif (isset($this->column)) {
			$column = $this->column->getValue();
			
			if (empty($column)) {
				$column = $this->column->getArgument();
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
			$value = $this->column->getValue();
			
			if (empty($value)) {
				$value = $this->column->getArgument();
			}
			
			$property = $parentProfile->getPropertyByColumn($value);
			$parameter = $property->getReflectionProperty()->getValue($entity);
			
			//build predicate
			$field = Column::__callstatic($entityProfile->getPrimaryKey(true));
			$predicate = $field->eq($parameter);
		}
		elseif (isset($this->attribute)) {
			$value = $this->attribute->getValue();
				
			if (empty($value)) {
				$value = $this->attribute->getArgument();
			}
			
			$property = $parentProfile->getProperty($value);
			$parameter = $property->getReflectionProperty()->getValue($entity);
			
			$field = Attr::__callstatic($entityProfile->getPrimaryKey());
			$predicate = $field->eq($parameter);
		}
		
		return $predicate;
	}
	
	protected function fetchValue(Manager $manager) {
		return $manager->get();
	}
}
?>