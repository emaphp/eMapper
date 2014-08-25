<?php
namespace eMapper\Reflection\Profile\Association;

use eMapper\Manager;
use eMapper\Reflection\Profiler;
use eMapper\Query\Column;

/**
 * The ManyToMany class is an abstraction of many-to-many associations.
 * @author emaphp
 */
class ManyToMany extends AbstractAssociation {
	public function buildAssociationJoin($joinAlias, $mainAlias) {
		$parentProfile = Profiler::getClassProfile($this->parent);
		$entityProfile = Profiler::getClassProfile($this->profile);
		
		$joinTable = $this->joinWith->getArgument();
		$joinTableAlias = $joinAlias . $mainAlias;
		$joinTableColumn = $this->joinWith->getValue();
		
		$column = $this->column->getValue();
		
		if (empty($column)) {
			$column = $this->column->getArgument();
		}
		
		return sprintf('INNER JOIN @@%s %s ON %s.%s = %s.%s INNER JOIN @@%s %s ON %s.%s = %s.%s',
				$joinTable, $joinTableAlias,
				$joinTableAlias, $joinTableColumn,
				$mainAlias, $entityProfile->getPrimaryKey(true),
				$parentProfile->getReferredTable(), $joinAlias,
				$joinTableAlias, $column,
				$joinAlias, $parentProfile->getPrimaryKey(true));
	}
	
	public function buildJoin($alias, $mainAlias) {
		$parentProfile = Profiler::getClassProfile($this->parent);
		$entityProfile = Profiler::getClassProfile($this->profile);
		
		$joinTable = $this->joinWith->getArgument();
		$joinTableAlias = $alias . $mainAlias;
		$joinTableColumn = $this->joinWith->getValue();

		$column = $this->column->getArgument();
		
		if (empty($column)) {
			$column = $this->column->getValue();
		}
		
		return sprintf('INNER JOIN @@%s %s ON %s.%s = %s.%s INNER JOIN @@%s %s ON %s.%s = %s.%s',
						$joinTable, $joinTableAlias,
						$joinTableAlias, $column,
						$mainAlias, $parentProfile->getPrimaryKey(true),
						$entityProfile->getReferredTable(), $alias,
						$joinTableAlias, $joinTableColumn,
						$alias, $entityProfile->getPrimaryKey(true));
	}
	
	public function buildCondition($entity) {
		$parentProfile = Profiler::getClassProfile($this->parent);
		
		$pk = $parentProfile->getProperty($parentProfile->getPrimaryKey());
		$parameter = $pk->getReflectionProperty()->getValue($entity);
		
		$field = Column::__callstatic($parentProfile->getPrimaryKey(true));
		return $field->eq($parameter);
	}
	
	public function fetchValue(Manager $manager) {
		return $manager->find();
	}
}
?>