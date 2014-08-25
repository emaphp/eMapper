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
			$property = $this->attribute->getValue();
				
			if (empty($property)) {
				$property = $this->attribute->getArgument();
			}
				
			$property = $entityProfile->getProperty($property);
			$column = $property->getColumn();
		}
		elseif (isset($this->column)) {
			$column = $this->column->getValue();
				
			if (empty($column)) {
				$column = $this->column->getArgument();
			}
		}
		
		return sprintf('INNER JOIN @@%s %s ON %s.%s = %s.%s',
					   $parentProfile->getReferredTable(true), $alias,
					   $mainAlias, $column,
					   $alias, $parentProfile->getPrimaryKey(true));
	}
	
	public function buildCondition($entity) {
		$parentProfile = Profiler::getClassProfile($this->parent);
		
		if (isset($this->column)) {
			$value = $this->column->getValue();
				
			if (empty($value)) {
				$value = $this->column->getArgument();
			}
				
			$pk = $parentProfile->getPropertyByColumn($parentProfile->getPrimaryKey(true));
			$parameter = $pk->getReflectionProperty()->getValue($entity);
				
			//build predicate
			$field = Column::__callstatic($value);
			$predicate = $field->eq($parameter);
		}
		elseif (isset($this->attribute)) {
			$value = $this->attribute->getValue();
		
			if (empty($value)) {
				$value = $this->attribute->getArgument();
			}
				
			$pk = $parentProfile->getProperty($parentProfile->getPrimaryKey());
			$parameter = $pk->getReflectionProperty()->getValue($entity);
				
			$field = Attr::__callstatic($value);
			$predicate = $field->eq($parameter);
		}
		
		return $predicate;
	}
	
	public function fetchValue(Manager $manager) {
		return $manager->find();
	}
}
?>