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
	/**
	 * Builds the SQL join for a many-to-many association
	 * @param string $joinAlias
	 * @param string $mainAlias
	 * @return string
	 */
	public function buildAssociationJoin($joinAlias, $mainAlias) {
		$parentProfile = Profiler::getClassProfile($this->parent);
		$entityProfile = Profiler::getClassProfile($this->profile);
		
		$joinTableAlias = $joinAlias . $mainAlias;
		
		//get join table
		$joinTable = $this->joinWith->getArgument();
		
		if (empty($joinTable)) {
			throw new \RuntimeException(sprintf("Association %s in class %s does not define a join table", $this->name, $this->parent));
		}
		
		//get join column
		$joinTableColumn = $this->joinWith->getValue();
		
		if (empty($joinTableColumn) || $joinTableColumn === true) {
			throw new \RuntimeException(sprintf("Association %s in class %s must specify a join column for table %s", $this->name, $this->parent, $joinTable));
		}
		
		//get foreign key
		$column = $this->column->getArgument();
		
		if (empty($column)) {
			$column = $this->column->getValue();
			
			if (empty($column) || $column === true) {
				throw new \RuntimeException(sprintf("Association %s in class %s does not define a foreign key column", $this->name, $this->parent));
			}
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
		
		$joinTableAlias = $alias . $mainAlias;
		
		//get join table
		$joinTable = $this->joinWith->getArgument();
		
		if (empty($joinTable)) {
			throw new \RuntimeException(sprintf("Association %s in class %s does not define a join table", $this->name, $this->parent));
		}
		
		//get join column
		$joinTableColumn = $this->joinWith->getValue();
		
		if (empty($joinTableColumn) || $joinTableColumn === true) {
			throw new \RuntimeException(sprintf("Association %s in class %s must specify a join column for table %s", $this->name, $this->parent, $joinTable));
		}

		//get foreign key
		$column = $this->column->getArgument();
		
		if (empty($column)) {
			$column = $this->column->getValue();
			
			if (empty($column) || $column === true) {
				throw new \RuntimeException(sprintf("Association %s in class %s does not define a foreign key column", $this->name, $this->parent));
			}
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