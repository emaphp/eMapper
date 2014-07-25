<?php
namespace eMapper;

use eMapper\Reflection\Profile\ClassProfile;
use eMapper\SQL\Configuration\StatementConfiguration;
use eMapper\Reflection\EntityMapper;
use eMapper\Query\Predicate\Filter;
use eMapper\Query\Predicate\SQLPredicate;
use eMapper\Query\Builder\DeleteQueryBuilder;
use eMapper\Query\Attr;
use eMapper\Query\Builder\InsertQueryBuilder;
use eMapper\Query\Builder\UpdateQueryBuilder;
use eMapper\Query\Builder\SelectQueryBuilder;
use eMapper\Query\Field;
use eMapper\Query\Column;
use eMapper\Query\Aggregate\SQLCount;
use eMapper\Query\Aggregate\SQLFunction;
use eMapper\Query\Aggregate\SQLAverage;
use eMapper\Query\Aggregate\SQLMax;
use eMapper\Query\Aggregate\SQLMin;
use eMapper\Query\Aggregate\SQLSum;

class Manager {
	use StatementConfiguration;
	use EntityMapper;
	
	/**
	 * Database mapper
	 * @var Mapper
	 */
	protected $mapper;
	
	/**
	 * Entity class profile
	 * @var ClassProfile
	 */
	protected $entity;
	
	/**
	 * Class mapping expression
	 * @var string
	 */
	protected $expression;
	
	public function __construct(Mapper $mapper, ClassProfile $entity) {
		$this->mapper = $mapper;
		$this->entity = $entity;
		
		//default mapping expression
		$this->expression = $this->buildExpression($entity);
	}
	
	/**
	 * Obtains current mapper instance
	 * @return \eMapper\Mapper
	 */
	public function getMapper() {
		return $this->mapper;
	}
	
	/**
	 * Obtains associated entity profile
	 * @return \eMapper\Reflection\Profile\ClassProfile
	 */
	public function getEntity() {
		return $this->entity;
	}
	
	/**
	 * Obtains current query mapping expression
	 * @return string
	 */
	protected function getMappingExpression() {
		$group = array_key_exists('query.group', $this->config) ? $this->config['query.group'] : null;
		$index = array_key_exists('query.index', $this->config) ? $this->config['query.index'] : null;
		return $this->buildListExpression($this->entity, $index, $group);
	}

	/**
	 * Removes manager-only options from configuration values
	 * @param string $values
	 * @return multitype:
	 */
	protected function clean_options($values = null) {
		$clean = array_diff_key($this->config, array_flip(['query.filter', 'query.index', 'query.group', 'query.distinct', 'query.columns', 'query.attrs', 'query.lefT_limit', 'query.right_limit', 'query.order_by']));
		
		if (is_array($values)) {
			return array_merge($clean, $values);
		}
		
		return $clean;
	}
	
	/**
	 * Finds an entity by the given id
	 * @param mixed $pk
	 * @throws \RuntimeException
	 * @return object
	 */
	public function findByPK($pk) {
		//connect to database
		$this->mapper->connect();
		
		//get primary key field
		$primaryKey = $this->entity->primaryKey;
	
		if (is_null($primaryKey)) {
			throw new \RuntimeException(sprintf("Class %s does not appear to have a primary key", $this->entity->reflectionClass->getName()));
		}
	
		//build query
		$query = new SelectQueryBuilder($this->entity);
		$query->setCondition(Attr::__callstatic($primaryKey)->eq($pk));
		list($query, $args) = $query->build($this->mapper->driver, $this->config);
		
		//run query
		$options = $this->clean_options(['map.type' => $this->expression]);
		return $this->mapper->merge($options)->query($query, $args);
	}
	
	/**
	 * Obtains a list of entities by the given condition
	 * @param SQLPredicate $condition
	 * @return mixed
	 */
	public function find(SQLPredicate $condition = null) {
		//connect to database
		$this->mapper->connect();
		
		//build query
		$query = new SelectQueryBuilder($this->entity);
		$query->setCondition($condition);
		list($query, $args) = $query->build($this->mapper->driver, $this->config);
		
		//run query
		$options = $this->clean_options(['map.type' => $this->getMappingExpression()]);
		return $this->mapper->merge($options)->query($query, $args);
	}
	
	/**
	 * Returns the first result from a query 
	 * @param SQLPredicate $condition
	 * @return NULL|object
	 */
	public function get(SQLPredicate $condition = null) {
		//connect to database
		$this->mapper->connect();
		
		//build query
		$query = new SelectQueryBuilder($this->entity);
		$query->setCondition($condition);
		list($query, $args) = $query->build($this->mapper->driver, $this->config);
		
		//run query
		$options = $this->clean_options(['map.type' => $this->expression]);
		return $this->mapper->merge($options)->query($query, $args);
	}
	
	/**
	 * Executes a declared statement
	 * @param string $statementId
	 * @return mixed
	 */
	public function execute($statementId) {
		//obtain parameters
		$args = func_get_args();
		$statementId = array_shift($args);
		
		if (!is_string($statementId) || empty($statementId)) {
			$this->driver->throw_exception("Statement id is not a valid string");
		}
		
		//get current namespace
		$ns = $this->entity->getNamespace();
		
		//build statement id
		if (isset($ns)) {
			$route = explode('.', $statementId);
			array_unshift($route, $ns);
			$statementId = implode('.', $route);
		}
		
		//obtain statement
		$stmt = $this->mapper->getStatement($statementId);
		
		if ($stmt === false) {
			$this->mapper->driver->throw_exception("Statement '$statementId' could not be found");
		}
		
		//get statement config
		$query = $stmt->query;
		$options = is_null($stmt->options) ? [] : $stmt->options->config;
		
		//add query to method parameters
		array_unshift($args, $query);
		
		//merge options
		$options = array_merge($options, $this->clean_options());
		
		return call_user_func_array([$this->mapper->merge($options), 'query'], $args);
	}
	
	/**
	 * Obtains an entity primary key value
	 * @param object $entity
	 * @throws \RuntimeException
	 */
	protected function getPrimaryKeyValue($entity) {
		$primaryKey = $this->entity->primaryKey;
			
		if (is_null($primaryKey)) {
			throw new \RuntimeException(sprintf("Class %s does not appear to have a primary key", $this->entity->reflectionClass->getName()));
		}
		
		//get primary key value
		$pkProperty = $this->entity->getReflectionProperty($property);
		return $pkProperty->getValue($entity);
	}
	
	/**
	 * Stores an instance into the database
	 * @param object $entity
	 * @throws \RuntimeException
	 * @return boolean|integer
	 */
	public function save($entity) {
		//connect to database
		$this->mapper->connect();
		
		//get primary key
		$pk = $this->getPrimaryKeyValue($entity);
		
		if (is_null($pk)) {
			//build insert query
			$query = new InsertQueryBuilder($this->entity);
			list($query, $_) = $query->build($this->mapper->driver);
			return $this->mapper->query($query, $entity);
		}
		
		//build update query
		$query = new UpdateQueryBuilder($this->entity);
		$query->setCondition(Attr::__callstatic($this->entity->primaryKey)->eq($pk));
		list($query, $args) = $query->build($this->mapper->driver);
		$this->mapper->query($query, $entity, $args);
		return $this->mapper->lastId();
	}
	
	/**
	 * Removes given entity from database
	 * @param object $entity
	 * @throws \RuntimeException
	 * @return boolean
	 */
	public function delete($entity) {
		//connect to database
		$this->mapper->connect();
		
		//get primary key
		$pk = $this->getPrimaryKeyValue($entity);
		
		//build query
		$query = new DeleteQueryBuilder($this->entity);
		$condition = Attr::__callStatic($this->entity->primaryKey)->eq($pk);
		$query->setCondition($condition);
		list($query, $args) = $query->build($this->mapper->driver);
		
		//run query
		return $this->mapper->query($query, $args);
	}
	
	/**
	 * Removes a set of entities by a given condition
	 * @param SQLPredicate $condition
	 * @return boolean
	 */
	public function deleteWhere(SQLPredicate $condition = null) {
		//connect to database
		$this->mapper->connect();
		
		//build query
		$query = new DeleteQueryBuilder($this->entity);
		$query->setCondition($condition);
		list($query, $args) = $query->build($this->mapper->driver, $this->config);
		
		//run query
		return $this->mapper->query($query, $args);
	}
	
	/**
	 * Truncates a table
	 * @return boolean
	 */
	public function truncate() {
		//connect to database
		$this->mapper->connect();
		
		//build query
		$query = new DeleteQueryBuilder($this->entity, true);
		list($query, $_) = $query->build($this->mapper->driver);
		return $this->mapper->query($query);
	}
	
	/*
	 * FUNCTIONS
	 */
	
	protected function sqlFunction(SQLFunction $function, $type) {
		//connect to database
		$this->mapper->connect();
		
		//build query
		$query = new SelectQueryBuilder($this->entity);
		$query->setFunction($function);
		list($query, $args) = $query->build($this->mapper->driver, $this->config);
		
		//run query
		$options = $this->clean_options(['map.type' => $type]);
		return $this->mapper->merge($options)->query($query, $args);
	}
	
	/**
	 * Obtains the total amount of rows
	 * @param string $type
	 * @return integer
	 */
	public function count($type = 'int') {
		return $this->sqlFunction(new SQLCount(), $type);
	}
	
	/**
	 * Obtains the average value for the given field
	 * @param Field $field
	 * @param string $type
	 * @return float
	 */
	public function avg(Field $field, $type = 'float') {
		return $this->sqlFunction(new SQLAverage($field), $type);
	}
	
	/**
	 * Obtains the maximum value for the given field
	 * @param Field $field
	 * @param string $type
	 * @return float
	 */
	public function max(Field $field, $type = 'float') {
		return $this->sqlFunction(new SQLMax($field), $type);
	}
	
	/**
	 * Obtains the minimum value for the given field
	 * @param Field $field
	 * @param string $type
	 * @return float
	 */
	public function min(Field $field, $type = 'float') {
		return $this->sqlFunction(new SQLMin($field), $type);
	}
	
	/**
	 * Obtains the sum of the given field
	 * @param Field $field
	 * @param string $type
	 * @return float
	 */
	public function sum(Field $field, $type = 'float') {
		return $this->sqlFunction(new SQLSum($field), $type);
	}
	
	/**
	 * Sets indexation column/callback
	 * @param Field $index
	 * @return Manager
	 */
	public function index(Field $index) {
		if ($index instanceof Attr) {
			$type = $index->getType();
			
			if (isset($type)) {
				return $this->merge(['query.index' => $index->getName() . ':' . $type]);
			}
			
			return $this->merge(['query.index' => $index->getName()]);
		}
		elseif ($index instanceof Column) {
			if (!in_array($index->getName(), $this->entity->fieldNames)) {
				throw new \InvalidArgumentException(sprintf("Cannot index by non declared column %s in class %s", $index->getName(), $this->entity->reflectionClass->getName()));
			}
			
			$property = $this->entity->columnNames[$index->getName()];
			
			if (isset($type)) {
				return $this->merge(['query.index' => $property . ':' . $type]);
			}
				
			return $this->merge(['query.index' => $property]);
		}
		
		throw new \InvalidArgumentException("Index must be specified as valid Field instance");
	}
	
	/**
	 * Sets grouping column/callback
	 * @param Field $group
	 * @return Manager
	 */
	public function group(Field $group) {
		if ($group instanceof Attr) {
			$type = $group->getType();
				
			if (isset($type)) {
				return $this->merge(['query.group' => $group->getName() . ':' . $type]);
			}
				
			return $this->merge(['query.group' => $group->getName()]);
		}
		elseif ($group instanceof Column) {
			if (!in_array($group->getName(), $this->entity->fieldNames)) {
				throw new \InvalidArgumentException(sprintf("Cannot index by non declared column %s in class %s", $group->getName(), $this->entity->reflectionClass->getName()));
			}
			
			$property = $this->entity->columnNames[$group->getName()];
				
			if (isset($type)) {
				return $this->merge(['query.group' => $property . ':' . $type]);
			}
			
			return $this->merge(['query.group' => $property]);
		}
		
		throw new \InvalidArgumentException("Group must be specified as a valid Field instance");
	}
	
	/**
	 * Sets query order
	 * @return Manager
	 */
	public function order_by() {
		return $this->merge(['query.order' => func_get_args()]);
	}
	
	/**
	 * Sets query row limit
	 * @param int $from
	 * @param int $to
	 * @return Manager
	 */
	public function limit($from, $to = null) {
		if (isset($to)) {
			return $this->merge(['query.from' => intval($from), 'query.to' => intval($to)]);
		}
		
		return $this->merge(['query.from' => intval($from)]);
	}
	
	/**
	 * Sets columns to obtain
	 * @return Manager
	 */
	public function columns() {
		return $this->merge(['query.columns' => func_get_args()]);
	}
	
	/**
	 * Filters results by a condition
	 * @return Manager
	 */
	public function filter() {
		return $this->push('query.filter', new Filter(func_get_args()));
	}
	
	/**
	 * Excludes results by a condition
	 * @return Manager
	 */
	public function exclude() {
		return $this->push('query.filter', new Filter(func_get_args(), true));
	}
	
	/**
	 * Defines if current query uses the distinct clause
	 * @return Manager
	 */
	public function distinct() {
		return $this->merge(['query.distinct' => true]);
	}
}
?>