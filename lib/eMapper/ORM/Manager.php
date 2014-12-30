<?php
namespace eMapper\ORM;

use eMapper\Reflection\PropertyAccessor;
use eMapper\Statement\Configuration\StatementConfiguration;
use eMapper\Reflection\EntityMapper;
use eMapper\Reflection\ClassProfile;
use eMapper\SQL\Predicate\Filter;
use eMapper\SQL\Predicate\SQLPredicate;
use eMapper\Query\Attr;
use eMapper\Query\Cond;
use eMapper\Query\Column;
use eMapper\Query\Func;
use eMapper\Query\Schema;
use eMapper\SQL\Aggregate\SQLFunction;
use eMapper\SQL\Aggregate\SQLCount;
use eMapper\SQL\Aggregate\SQLAverage;
use eMapper\SQL\Aggregate\SQLSum;
use eMapper\SQL\Aggregate\SQLMin;
use eMapper\SQL\Aggregate\SQLMax;
use eMapper\Mapper;


/**
 * The Manager class provides a common interface for obtaining data related to an entity.
 * @author emaphp
 */
class Manager {
	use StatementConfiguration;
	use EntityMapper;
	use PropertyAccessor;
		
	/**
	 * Mapper instance
	 * @var \eMapper\Mapper
	 */
	protected $mapper;
	
	/**
	 * Entity profile
	 * @var \eMapper\Reflection\ClassProfile
	 */
	protected $entityProfile;
	
	/**
	 * Entity select columns
	 * @var array:\eMapper\Query\Column
	 */
	protected $selectColumns = [];
	
	/**
	 * Entity insert columns
	 * @var array[string]:string
	 */
	protected $insertColumns = [];
	
	/**
	 * Additional configuration keys
	 * @var array:string
	 */
	protected static $options = [
		'query.filter', 'query.index', 'query.group', 'query.distinct',
		'query.attrs', 'query.limit', 'query.offset', 'query.orderBy', 'query.negate'
	];
	
	public function __construct(Mapper $mapper, ClassProfile $profile) {
		$this->mapper = $mapper;
		$this->entityProfile = $profile;
		
		//additional attributes lists
		foreach ($profile->getSelectAttributes() as $attr)
			$this->selectColumns[] = new Column($profile->getProperty($attr)->getColumn());
		
		foreach ($profile->getInsertAttributes() as $attr)
			$this->insertColumns[$profile->getProperty($attr)->getColumn()] = $attr;
	}
	
	/*
	 * CONFIG HELPERS
	 */
	
	protected function clean($values, $reverse = true) {
		$clean = array_diff_key($this->config, array_flip(self::$options));
		return $reverse ? array_merge($values, $clean) : array_merge($clean, $values);
	}
	
	/**
	 * Obtains a list mapping expression for the current configuration
	 * @return string
	 */
	protected function getListMappingExpression() {
		$group = $this->hasOption('query.group') ? $this->getOption('query.group') : null;
		$index = $this->hasOption('query.index') ? $this->getOption('query.index') : null;
		return $this->buildListExpression($this->entityProfile, $index, $group);
	}
	
	/**
	 * Obtains a list of Column instances with the list of columns to fetch
	 * @throws \InvalidArgumentException
	 * @return array:\eMapper\Query\Column
	 */
	protected function getSelectColumns() {
		//obtain attributes from config
		if ($this->hasOption('query.attrs')) {
			$columns = [];
			$attrs = $this->getOption('query.attrs');
			
			foreach ($attrs as $attr) {
				if ($attr instanceof Attr)
					$name = $attr->getName();				
				elseif (is_string($attr))
					$name = $attr;
				else
					throw new \InvalidArgumentException("Method 'attrs' expects a list of strings or instances of \eMapper\Query\Attr");
				
				if (!$this->entityProfile->hasProperty($name))
					throw new \InvalidArgumentException(sprintf("Attribute '%s' not found in class '%s'", $name, $this->entityProfile->getReflectionClass()->getName()));
				
				$column = $this->entityProfile->getProperty($name)->getColumn();
				if (!array_key_exists($column, $columns))
					$columns[$column] = new Column($column);
			}
			
			return $columns;
		}
	
		return $this->selectColumns;
	}
	
	/**
	 * Obtains an array containing an entity data to insert/update
	 * @param object
	 * @return array
	 */
	protected function entityToArray($entity) {
		$value = [];
		foreach ($this->insertColumns as $column => $attr)
			$value[$column] = $this->getPropertyValue($this->entityProfile, $entity, $attr);		
		return $value;
	}
	
	/**
	 * Obtains configured filter
	 * @return \eMapper\SQL\Predicate\SQLPredicate
	 */
	protected function getFilter() {
		$filter = $this->getOption('query.filter');
		$negate = false;
		if ($this->hasOption('query.negate'))
			$negate = (bool) $this->getOption('query.negate');
		if (is_array($filter))
			return new Filter($filter, $negate);
		return new Filter([$filter], $negate);
	}
	
	/**
	 * Obtains the list of ordering columns
	 * @throws \InvalidArgumentException
	 * @return array:\eMapper\Query\Column | NULL
	 */
	protected function getOrderBy() {
		$order = [];
			
		foreach ($this->getOption('query.orderBy') as $attr) {
			$type = null;
			
			if ($attr instanceof Attr) {
				$name = $attr->getName();
				$type = $attr->getType();
			}
			elseif (is_string($attr)) {
				$expr = explode(' ', $attr);
				$name = $expr[0];
				
				if (count($expr) > 1) {
					$type = strtolower($expr[1]);
					if ($type != 'asc' && $type != 'desc')
						$type = null;
				}	
			}
			else
				throw new \InvalidArgumentException("Invalid order attribute defined: Expected a string or \eMapper\Query\Attr instance");
			
			$order[] = Attr::__callstatic($name)->type($type);
		}
		
		return $order;
	}
	
	/*
	 * CONFIG
	 */
	
	/**
	 * Sets index attribute
	 * @param string $index
	 * @return \eMapper\ORM\Manager
	 */
	public function index($index = null) {
		if (is_null($index))
			return $this->discard('query.index');
	
		if ($index instanceof Column) {
			//get referred attribute
			$map = $this->entityProfile->getPropertyMap();
			if (!in_array($index->getName(), $map))
				throw new \InvalidArgumentException(sprintf("Column '%s' is unknown for class '%s'", $index->getName(), $this->entityProfile->getReflectionClass()->getName()));
			$name = array_flip($map)[$index->getName()];
		}
		elseif ($index instanceof Attr)
			$name = $index->getName();
		elseif (is_string($index))
			$name = $index;
		else
			throw new \InvalidArgumentException("Index must be specified through an Attr instance or a valid property name");
			
		//get custom type (if any)
		$type = $index->getType();	
		if (isset($type))
			return $this->merge(['query.index' => $name. ':' . $type]);
		return $this->merge(['query.index' => $name]);
	}
	
	/**
	 * Sets group attribute
	 * @param string $group
	 * @return \eMapper\ORM\Manager
	 */
	public function group($group = null) {
		if (is_null($group))
			return $this->discard('query.group');
	
		if ($group instanceof Column) {
			//get referred attribute
			$map = $this->entityProfile->getPropertyMap();
			if (!in_array($group->getName(), $map))
				throw new \InvalidArgumentException(sprintf("Column '%s' is unknown for class '%s'", $group->getName(), $this->entityProfile->getReflectionClass()->getName()));
			$name = array_flip($map)[$group->getName()];
		}
		elseif ($group instanceof Attr)
			$name = $group->getName();
		elseif (is_string($group))
			$name = $group;
		else
			throw new \InvalidArgumentException("Group must be specified through an Attr instance or a valid property name");
		
		//get custom type (if any)
		$type = $group->getType();
		if (isset($type))
			return $this->merge(['query.group' => $group->getName() . ':' . $type]);
		return $this->merge(['query.group' => $group->getName()]);
	}
	
	/**
	 * Sets order attributes
	 * @param string $order
	 * @return \eMapper\ORM\Manager
	 */
	public function orderBy($order = null) {
		if (is_null($order)) //remove option
			return $this->discard('query.orderBy');
			
		return $this->merge(['query.orderBy' => func_get_args()]);
	}
	
	/**
	 * Sets rows limit
	 * @param string $limit
	 * @return \eMapper\ORM\Manager
	 */
	public function limit($limit = null) {
		if (is_null($limit)) //remove options
			return $this->discard('query.limit');
	
		return $this->merge(['query.limit' => intval($limit)]);
	}
	
	/**
	 * Sets rows offset
	 * @param string $offset
	 * @return \eMapper\ORM\Manager
	 */
	public function offset($offset = null) {
		if (is_null($offset)) //remove options
			return $this->discard('query.offset');
		
		return $this->merge(['query.offset' => intval($offset)]);
	}
	
	/**
	 * Sets a new filter
	 * @return \eMapper\ORM\Manager
	 */
	public function filter() {
		return $this->append('query.filter', new Filter(func_get_args()));
	}
	
	/**
	 * Sets a new filter
	 * @return \eMapper\ORM\Manager
	 */
	public function exclude() {
		return $this->append('query.filter', new Filter(func_get_args(), true));
	}
	
	/**
	 * Sets a new filter
	 * @return \eMapper\ORM\Manager
	 */
	public function orfilter() {
		return $this->append('query.filter', new Filter(func_get_args(), false, Cond::LOGICAL_OR));
	}
	
	/**
	 * Sets a new filter
	 * @return \eMapper\ORM\Manager
	 */
	public function orexclude() {
		return $this->append('query.filter', new Filter(func_get_args(), true, Cond::LOGICAL_OR));
	}
	
	/**
	 * Obtains only distinct rows
	 * @param boolean $distinct
	 * @return \eMapper\ORM\Manager
	 */
	public function distinct($distinct = true) {
		return $this->merge(['query.distinct' => $distinct]);
	}
	
	/**
	 * Sets the attributes to fetch
	 * @param array $attrs
	 * @return \eMapper\ORM\Manager
	 */
	public function attrs($attrs) {
		if (is_array($attrs) && !empty($attrs))
			return $this->merge(['query.attrs' => $attrs]);
		else
			return $this->merge(['query.attrs' => func_get_args()]);
	}
	
	/**
	 * Negates current filter
	 * @param boolean $negate
	 * @return \eMapper\ORM\Manager
	 */
	public function negate($negate = true) {
		return $this->merge(['query.negate' => (bool)$negate]);
	}
	
	/*
	 * TRIGGERS
	 */	
		
	/**
	 * Obtains an entity by primary key
	 * @param mixed $pk
	 * @return object
	 */
	public function findByPk($pk) {
		$this->mapper->connect();
		
		//build fluent query
		$query = $this->mapper->newQuery()
		->from($this->entityProfile->getEntityTable())
		->select($this->getSelectColumns())
		->where(Column::__callstatic($this->entityProfile->getPrimaryKey(true))->eq($pk));
		
		//run query
		list($sql, $args) = $query->build();
		return $this->mapper->merge($this->clean(['map.type' => $this->buildExpression($this->entityProfile)]))->execute($sql, $args);
	}
	
	/**
	 * Finds a list of entities by a given criteria
	 * @param \eMapper\SQL\Predicate\SQLPredicate $condition
	 * @return array:object
	 */
	public function find(SQLPredicate $condition = null) {
		$this->mapper->connect();
		$table = $this->entityProfile->getEntityTable();
		
		//build fluent query
		$query = $this->mapper->newQuery($this->entityProfile)
		->from($table, Schema::DEFAULT_ALIAS)
		->select($this->getSelectColumns());
		
		//set query condition
		$args = func_get_args();
		if (empty($args) && $this->hasOption('query.filter'))
			$query->where($this->getFilter());
		elseif (isset($condition))
			$query->where(new Filter($args));
		
		//order by
		if ($this->hasOption('query.orderBy'))
			$query->orderBy($this->getOrderBy());
		
		//limit + offset
		if ($this->hasOption('query.limit'))
			$query->limit($this->getOption('query.limit'));
		
		if ($this->hasOption('query.offset'))
			$query->offset($this->getOption('query.offset'));
		
		//distinct
		if ($this->hasOption('query.distinct') && $this->getOption('query.distinct'))
			$query->distinct();
		
		list($sql, $args) = $query->build();
		return $this->mapper->merge($this->clean(['map.type' => $this->getListMappingExpression()]))->execute($sql, $args);
	}
	
	/**
	 * Gets an unique object bi a given criteria
	 * @param \eMapper\SQL\Predicate\SQLPredicate $condition
	 * @return object
	 */
	public function get(SQLPredicate $condition = null) {
		$this->mapper->connect();
		$table = $this->entityProfile->getEntityTable();
		
		//build fluent query
		$query = $this->mapper->newQuery($this->entityProfile)
		->from($table, Schema::DEFAULT_ALIAS)
		->select($this->getSelectColumns());
		
		//set query condition
		$args = func_get_args();
		if (empty($args) && $this->hasOption('query.filter'))
			$query->where($this->getOption('query.filter'));
		elseif (isset($condition))
			$query->where(new Filter($args));
		
		//order by
		if ($this->hasOption('query.orderBy'))
			$query->orderBy($this->getOrderBy());
		
		list($sql, $args) = $query->build();
		return $this->mapper->merge($this->clean(['map.type' => $this->buildExpression($this->entityProfile)]))->execute($sql, $args);
	}
	
	/**
	 * Stores an entity instance into the database
	 * @param object $entity
	 * @throws \RuntimeException
	 * @return boolean | integer
	 */
	public function save(&$entity, $depth = 1) {
		if (is_null($entity))
			return null;
		
		//connect to database
		$this->mapper->connect();
		
		//begin transaction
		$this->mapper->beginTransaction();
		
		if ($depth == 0) { //don't store associated data
			//get primary key value
			$pk = $this->getPropertyValue($this->entityProfile, $entity, $this->entityProfile->getPrimaryKey());
			
			if (is_null($pk)) { //new instance
				//check for duplicated values
				$checks = $this->entityProfile->getDuplicateChecks();
				
				if (!empty($checks)) {
					$ignore = true;
					
					foreach ($checks as $attr) {
						//get unique field value
						$attribute = $this->entityProfile->getProperty($attr);
						$value = $this->getPropertyValue($this->entityProfile, $entity, $attr);
						
						//get duplicate value id
						$query = $this->mapper->newQuery();
						$pk = $query->from($this->entityProfile->getEntityTable())
						->select(new Column($this->entityProfile->getPrimaryKey(true)))
						->where(Column::__callstatic($attribute->getColumn())->eq($value))
						->fetch('i');
						
						if (!is_null($pk)) {
							$ignore = $attribute->getCheckDuplicate() == 'ignore';
							break;
						}
					}
					
					if (!is_null($pk)) { //duplicated value found
						$this->setPropertyValue($this->entityProfile, $entity, $this->entityProfile->getPrimaryKey(), $pk);
						//ignore or update
						return $ignore ? $pk : $this->save($entity, $depth);
					}
				}
				
				//build insert query
				$query = $this->mapper->newQuery();
				$query->insertInto($this->entityProfile->getEntityTable())
				->columns(array_keys($this->insertColumns))
				->valuesArray($this->entityToArray($entity))
				->exec();
				$pk = $this->mapper->getLastId();
				
				//update primary key property
				$this->setPropertyValue($this->entityProfile, $entity, $this->entityProfile->getPrimaryKey(), $pk);
				$this->mapper->commit();
				return $pk;
			}
			
			//build update query
			$query = $this->mapper->newQuery();
			$query->update($this->entityProfile->getEntityTable())
			->setValue($this->entityToArray($entity))
			->where(Column::__callstatic($this->entityProfile->getPrimaryKey(true))->eq($pk))
			->exec();
			$this->mapper->commit();
			return $pk;
		}
		
		$foreignKeys = [];
		
		//store parent instances
		if ($this->entityProfile->hasForeignKeys()) {
			$foreignKeys = $this->entityProfile->getForeignKeys();
			
			foreach ($foreignKeys as $attr => $assoc) {
				$assoc = $this->entityProfile->getAssociation($assoc);
				
				if ($assoc->isReadOnly())
					continue;
				
				$related = $this->getAssociationValue($this->entityProfile, $entity, $assoc);
				if (is_null($related))
					$this->setPropertyValue($this->entityProfile, $entity, $attr, null);
				else {
					$id = $assoc->save($this->mapper, $entity, $related, $depth - 1);
					if (!is_null($id))
						$this->setPropertyValue($this->entityProfile, $entity, $attr, $id);
				}
			}
		}
		
		//get primary key value
		$pk = $this->getPropertyValue($this->entityProfile, $entity, $this->entityProfile->getPrimaryKey());
		
		//new instance
		if (is_null($pk)) {
			//check for duplicated values
			$checks = $this->entityProfile->getDuplicateChecks();
			
			if (!empty($checks)) {
				$ignore = true;
					
				foreach ($checks as $attr) {
					//get unique field value
					$attribute = $this->entityProfile->getProperty($attr);
					$value = $this->getPropertyValue($this->entityProfile, $entity, $attr);
			
					//obtain duplicate value id
					$query = $this->mapper->newQuery();
					$pk = $query->from($this->entityProfile->getEntityTable())
					->select(new Column($this->entityProfile->getPrimaryKey(true)))
					->where(Column::__callstatic($attribute->getColumn())->eq($value))
					->fetch('i');
					
					if (!is_null($pk)) {
						$ignore = $attribute->getCheckDuplicate() == 'ignore';
						break;
					}
				}
					
				if (!is_null($pk)) {
					//persist or ignore
					$this->setPropertyValue($this->entityProfile, $entity, $this->entityProfile->getPrimaryKey(), $pk);
					return $ignore ? $pk : $this->save($entity, $depth);
				}
			}
			
			//build insert query
			$query = $this->mapper->newQuery();
			$query->insertInto($this->entityProfile->getEntityTable())
			->columns(array_keys($this->insertColumns))
			->valuesArray($this->entityToArray($entity))
			->exec();
			$pk = $this->mapper->getLastId();
			
			//update primary key property
			$this->setPropertyValue($this->entityProfile, $entity, $this->entityProfile->getPrimaryKey(), $pk);
		}
		else {
			//update value
			$query = $this->mapper->newQuery();
			$query->update($this->entityProfile->getEntityTable())
			->setValue($this->entityToArray($entity))
			->where(Column::__callstatic($this->entityProfile->getPrimaryKey(true))->eq($pk))
			->exec();
		}
		
		//persist related data
		foreach ($this->entityProfile->getAssociations() as $attr => $assoc) {
			if (in_array($attr, $foreignKeys)) //already persisted
				continue;
			
			if ($assoc->isReadOnly())
				continue;
			
			//get associated data
			$value = $this->getAssociationValue($this->entityProfile, $entity, $assoc);
			if (!is_null($value))
				$assoc->save($this->mapper, $entity, $value, $depth - 1);
		}
		
		$this->mapper->commit();
		return $pk;
	}
	
	/**
	 * Removes given entity from database
	 * @param array | object $entity
	 * @return boolean
	 */
	public function delete($entity) {
		if (is_null($entity))
			return;
		
		//connect to database
		$this->mapper->connect();
		
		//begin transaction
		$this->mapper->beginTransaction();
		
		//get primary key
		$pk = $this->getPropertyValue($this->entityProfile, $entity, $this->entityProfile->getPrimaryKey());

		foreach ($this->entityProfile->getReferences() as $assoc)
			$this->entityProfile->getAssociation($assoc)->delete($this->mapper, $pk);
		
		//build fluent query
		$query = $this->mapper->newQuery()
		->deleteFrom($this->entityProfile->getEntityTable())
		->where(Column::__callstatic($this->entityProfile->getPrimaryKey(true))->eq($pk));
		
		//run query
		$result = $query->exec();
		
		//commit
		$this->mapper->commit();
		return $result;
	}
	
	/**
	 * Removes a set of entities by a given condition
	 * @param SQLPredicate $condition
	 * @param boolean $cascade
	 * @return boolean
	 */
	public function deleteWhere(SQLPredicate $condition = null, $cascade = false) {
		//connect to database
		$this->mapper->connect();
		
		//begin transaction
		$this->mapper->beginTransaction();
		
		if ($cascade) {
			$list = $this->find($condition);
				
			foreach ($list as $entity)
				$this->delete($entity);
			
			$result = true;
		}
		else {
			$query = $this->mapper->newQuery($this->entityProfile)
			->deleteFrom($this->entityProfile->getEntityTable())
			->where($condition);
			
			//run query
			$result = $query->exec();
		}
		
		//commit
		$this->mapper->commit();
		return $result;
	}
	
	/**
	 * Truncates a table
	 * @return boolean
	 */
	public function truncate() {
		//connect to database
		$this->mapper->connect();
	
		//begin transaction
		$this->mapper->beginTransaction();
	
		//build query
		$query = $this->mapper->newQuery()
		->deleteFrom($this->entityProfile->getEntityTable());
			
		//run query
		$result = $query->exec();
	
		//commit
		$this->mapper->commit();
		return $result;
	}
	
	/*
	 * FUNCTIONS
	 */
	
	/**
	 * Invokes an aggregate function
	 * @param \eMapper\SQL\Aggregate\SQLFunction $function
	 * @throws \InvalidArgumentException
	 * @return mixed
	 */
	protected function sqlFunction(SQLFunction $function) {
		//connect to database
		$this->mapper->connect();
	
		//build query
		$query = $this->mapper->newQuery($this->entityProfile)
		->from($this->entityProfile->getEntityTable(), Schema::DEFAULT_ALIAS)
		->select($function->getFunctionInstance());
	
		//set query condition
		if ($this->hasOption('query.filter'))
			$query->where($this->getFilter());
	
		list($sql, $args) = $query->build();
		return $this->mapper->merge($this->clean(['map.type' => $function->getDefaultType()]))->execute($sql, $args);
	}
	
	/**
	 * @return int
	 */
	public function count() {
		return $this->sqlFunction(new SQLCount());
	}
	
	/**
	 * @param Attr | string $attr
	 * @return float
	 */
	public function avg($attr) {
		return $this->sqlFunction(new SQLAverage($attr));
	}
	
	/**
	 * @param Attr | string $attr
	 * @return float
	 */
	public function sum($attr) {
		return $this->sqlFunction(new SQLSum($attr));
	}
	
	/**
	 * @param Attr | string $attr
	 * @return float
	 */
	public function min($attr) {
		return $this->sqlFunction(new SQLMin($attr));
	}
	
	/**
	 * @param Attr | string $attr
	 * @return float
	 */
	public function max($attr) {
		return $this->sqlFunction(new SQLMax($attr));
	}
}