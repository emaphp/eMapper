<?php
namespace eMapper\ORM\Association;

use eMapper\SQL\Fluent\FluentSelect;
use eMapper\ORM\AssociationManager;
use eMapper\ORM\Manager;
use eMapper\Query\Column;
use eMapper\Mapper;
use Omocha\AnnotationBag;

/**
 * The ManyToMany class is an abstraction of many-to-many associations.
 * @author emaphp
 */
class ManyToMany extends Association {
	/**
	 * Join table
	 * @var string
	 */
	protected $joinTable;
	
	/**
	 * Parent class column
	 * @var string
	 */
	protected $parentColumn;
	
	/**
	 * Entity class column
	 * @var string
	 */
	protected $entityColumn;
	
	protected function parseConfig(AnnotationBag $propertyAnnotations) {
		parent::parseConfig($propertyAnnotations);
		
		//get join table
		$this->joinTable = $this->join->getValue();
		if (empty($this->joinTable) || !is_string($this->joinTable))
			throw new \RuntimeException(sprintf("Many-to-many association '%s' in class '%s' must define a join table through a @Join annotation", $this->name, $this->parentClass));
		
		//get join columns
		$join = $this->join->getArgument();
		if (empty($join))
			throw new \RuntimeException(sprintf("Many-to-many association '%s' in class '%s' must define a join column list for table '%s'", $this->name, $this->parentClass, $this->joinTable));
		$columns = explode(',', $join);
		if (count($columns) < 2)
			throw new \RuntimeException(sprintf("Many-to-many association '%s' in class '%s' doesn't define enough columns for join table '%s'", $this->name, $this->parentClass, $this->joinTable));
		
		$this->parentColumn = trim($columns[0]);
		$this->entityColumn = trim($columns[1]);
	}
	
	public function getJoinPredicate($entity) {
		//get primary key value
		$parentProfile = Profiler::getClassProfile($this->parentClass);
		$parameter = $this->getPropertyValue($parentProfile, $entity, $parentProfile->getPrimaryKey());
		
		//set appropiate prefix
		$joinTableAlias = Manager::DEFAULT_ALIAS . Manager::CONTEXT_ALIAS;
		
		//build predicate
		return Column::__callstatic($joinTableAlias . '__' . $this->parentColumn)->eq($parameter);
	}
	
	public function appendJoin(FluentSelect &$query, $mainAlias, $contextAlias) {
		$parentProfile = Profiler::getClassProfile($this->parentClass);
		$entityProfile = Profiler::getClassProfile($this->entityClass);
		
		//join table alias
		$joinTableAlias = $mainAlias . $contextAlias;
		
		//first join -> join table
		$cond = sprintf(
			'%s.%s = %s.%s',
			$joinTableAlias, $this->entityColumn,
			$mainAlias, $entityProfile->getPrimaryKey(true)
		);
		
		$query->innerJoin($this->joinTable, $joinTableAlias, $cond);
		
		//second join -> context
		$cond = sprintf(
			'%s.%s = %s.%s',
			$joinTableAlias, $this->parentColumn,
			$contextAlias, $parentProfile->getPrimaryKey(true)
		);
		
		$query->innerJoin($parentProfile->getEntityTable(), $contextAlias, $cond);
	}
	
	public function fetchValue(AssociationManager $manager) {
		return $manager->find();
	}
	
	public function save(Mapper $mapper, $parent, $value, $depth) {
		//if no array then return
		if ($value instanceof AssociationManager || !is_array($value))
			return null;
		
		$entityProfile = Profiler::getClassProfile($this->entityClass);
		$parentProfile = Profiler::getClassProfile($this->parentClass);
		
		//get foreign key value
		$foreignKey = $this->getPropertyValue($parentProfile, $parent, $parentProfile->getPrimaryKey());
		$manager = $mapper->newManager($this->entityClass);
		$keys = [];
		
		//save related data, store ids
		foreach ($value as &$entity)
			$keys[] = $manager->save($entity, $depth);
		
		//get join table associations
		$current = $mapper->newQuery()
		->from($this->joinTable)
		->select($this->entityColumn)
		->where(Column::__callstatic($this->parentColumn)->eq($foreignKey))
		->fetch('int[]');
		
		//get new elements
		$diff = array_diff($keys, $current);
		
		//if new elements are available add rows in join table
		if (!empty($diff)) {
			$query = $mapper->newQuery()
			->insertInto($this->joinTable)
			->columns($this->entityColumn, $this->parentColumn);
			
			foreach ($diff as $id) {
				$query->values($id, $foreignKey);
				$query->exec();
			}
		}
		
		//delete old data
		$query = $mapper->newQuery()
		->deleteFrom($this->joinTable)
		->where(Column::__callstatic($this->parentColumn)->eq($foreignKey), Column::__callstatic($this->entityColumn)->in($keys, false));
		$query->exec();
	}
	
	public function delete(Mapper $mapper, $foreignKey) {
		$query = $mapper->newQuery()
		->deleteFrom($this->joinTable)
		->where(Column::__callstatic($this->parentColumn)->eq($foreignKey));
		$query->exec();
	}
}