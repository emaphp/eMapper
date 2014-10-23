<?php
namespace eMapper\Reflection\Profile\Association;

use eMapper\Manager;
use eMapper\Reflection\Profiler;
use eMapper\Query\Column;
use eMapper\AssociationManager;
use Omocha\AnnotationBag;

/**
 * The ManyToMany class is an abstraction of many-to-many associations.
 * @author emaphp
 */
class ManyToMany extends Association {
	public function __construct($name, AnnotationBag $annotations, \ReflectionProperty $reflectionProperty) {
		parent::__construct('ManyToMany', $name, $annotations, $reflectionProperty);
	}
	
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
		
		if (empty($joinTable))
			throw new \RuntimeException(sprintf("Association %s in class %s does not define a join table", $this->name, $this->parent));
		
		//get join column		
		if (empty($this->foreignKey) || $this->foreignKey === true)
			throw new \RuntimeException(sprintf("Association %s in class %s must specify a join column for table %s", $this->name, $this->parent, $joinTable));
		
		//get foreign key
		$column = $this->joinWith->getValue();
		
		if (empty($column) || $column === true)
			throw new \RuntimeException(sprintf("Association %s in class %s does not define a foreign key column", $this->name, $this->parent));
		
		return sprintf('INNER JOIN @@%s %s ON %s.%s = %s.%s INNER JOIN @@%s %s ON %s.%s = %s.%s',
				$joinTable, $joinTableAlias,
				$joinTableAlias, $this->foreignKey,
				$mainAlias, $entityProfile->getPrimaryKey(true),
				$parentProfile->getReferredTable(), $joinAlias,
				$joinTableAlias, $column,
				$joinAlias, $parentProfile->getPrimaryKey(true));
	}
	
	public function buildJoin($alias, $mainAlias, $joinType) {
		$parentProfile = Profiler::getClassProfile($this->parent);
		$entityProfile = Profiler::getClassProfile($this->profile);
		
		$joinTableAlias = $alias . $mainAlias;
		
		//get join table
		$joinTable = $this->joinWith->getArgument();
		
		if (empty($joinTable))
			throw new \RuntimeException(sprintf("Association %s in class %s does not define a join table", $this->name, $this->parent));
		
		//get join column
		$joinTableColumn = $this->foreignKey;
		
		if (empty($joinTableColumn) || $joinTableColumn === true)
			throw new \RuntimeException(sprintf("Association %s in class %s must specify a join column for table %s", $this->name, $this->parent, $joinTable));

		//get foreign key
		$column = $this->joinWith->getValue();
		
		if (empty($column) || $column === true)
			throw new \RuntimeException(sprintf("Association %s in class %s does not define a foreign key column", $this->name, $this->parent));
		
		return sprintf('%s JOIN @@%s %s ON %s.%s = %s.%s %s JOIN @@%s %s ON %s.%s = %s.%s',
						$joinType,
						$joinTable, $joinTableAlias,
						$joinTableAlias, $column,
						$mainAlias, $parentProfile->getPrimaryKey(true),
						$joinType,
						$entityProfile->getReferredTable(), $alias,
						$joinTableAlias, $joinTableColumn,
						$alias, $entityProfile->getPrimaryKey(true));
	}
	
	public function buildCondition($entity) {
		$parentProfile = Profiler::getClassProfile($this->parent);
		
		//obtain primary key value
		$pk = $parentProfile->getProperty($parentProfile->getPrimaryKey());
		$parameter = $pk->getReflectionProperty()->getValue($entity);
		
		//build predicate
		$field = Column::__callstatic($parentProfile->getPrimaryKey(true));
		return $field->eq($parameter);
	}
	
	public function save($mapper, $parent, $value, $depth) {
		if ($value instanceof AssociationManager)
			return null;
	
		if (!is_array($value))
			return null;

		//get related profiles
		$entityProfile = Profiler::getClassProfile($this->profile);
		$parentProfile = Profiler::getClassProfile($this->parent);
		
		//build entity manager
		$manager = $mapper->buildManager($this->profile);
		
		//get foreign key value
		$foreignKey = $this->getPropertyValue($parentProfile, $parent, $parentProfile->getPrimaryKey());
		
		$keys = [];
		
		foreach ($value as &$entity)
			$keys[] = $manager->save($entity, $depth);
	
		//update join table
		$current = $mapper->type('int[]')->query(sprintf("SELECT %s FROM %s WHERE %s = %s", $this->foreignKey, $this->joinWith->getArgument(), $this->joinWith->getValue(), $foreignKey));
		$diff = array_diff($keys, $current);
		
		if (!empty($diff)) {
			foreach ($diff as $id)
				$mapper->sql(sprintf("INSERT INTO %s (%s, %s) VALUES (%s, %s)", $this->joinWith->getArgument(), $this->foreignKey, $this->joinWith->getValue(), $id, $foreignKey));
		}
		
		$query = sprintf("DELETE FROM %s WHERE %s = %s AND %s NOT IN (%s)", $this->joinWith->getArgument(), $this->joinWith->getValue(), $foreignKey, $this->foreignKey, implode(',', $keys));
		$mapper->sql($query);
		return null;
	}
	
	public function delete($mapper, $foreignKey) {
		$mapper->sql(sprintf("DELETE FROM %s WHERE %s = %s", $this->joinWith->getArgument(), $this->joinWith->getValue(), $foreignKey));
	}
	
	public function fetchValue(Manager $manager) {
		return $manager->find();
	}
}
?>