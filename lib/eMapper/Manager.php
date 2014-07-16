<?php
namespace eMapper;

use eMapper\Reflection\Profile\ClassProfile;
use eMapper\SQL\Configuration\StatementConfiguration;
use eMapper\Query\Predicate\Filter;
use eMapper\Query\Predicate\SQLPredicate;
use eMapper\Query\Builder\DeleteQueryBuilder;
use eMapper\Query\Attr;
use eMapper\Query\Builder\InsertQueryBuilder;
use eMapper\Query\Builder\CreateQueryBuilder;
use eMapper\Query\Builder\SelectQueryBuilder;

class Manager {
	use StatementConfiguration {
		index as index_callback;
		group as group_callbak;
	}
	
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
		$this->expression = 'obj:' . $entity->reflectionClass->getName();
	}
	
	public function getMapper() {
		return $this->mapper;
	}
	
	public function getEntity() {
		return $this->entity;
	}
	
	/**
	 * Obtains current query mapping expression
	 * @return string
	 */
	protected function getTypeExpression() {
		//build mapping expression
		$mappingExpression = $this->expression;
		
		//add grouping expression
		if (array_key_exists('query.group', $this->config)) {
			$mappingExpression .= '<' . $this->config['query.group'] . '>';
		}
		
		//add index expression
		if (array_key_exists('query.index', $this->config)) {
			$mappingExpression .= '[' . $this->config['query.index'] . ']';
		}
		else {
			$mappingExpression .= '[]';
		}
		
		return $mappingExpression;
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
		$options = $this->clean_options(['map.type' => $this->getTypeExpression()]);
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
		
		//build create query
		$query = new CreateQueryBuilder($this->entity);
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
		//build query
		$query = new DeleteQueryBuilder($this->entity, true);
		list($query, $_) = $query->build($this->mapper->driver);
		return $this->mapper->query($query);
	}
	
	/**
	 * Sets indexation column/callback
	 * @param mixed $index
	 * @return Manager
	 */
	public function index($index) {
		if ($index instanceof \Closure) {
			return $this->index_callback($index);
		}
		
		return $this->merge(['query.index' => $index]);
	}
	
	/**
	 * Sets grouping column/callback
	 * @param mixed $group
	 * @return Manager
	 */
	public function group($group) {
		if ($group instanceof \Closure) {
			return $this->group_callback($group);
		}
		
		return $this->merge(['query.group' => $group]);
	}
	
	/**
	 * Sets query order
	 * @return Manager
	 */
	public function orderBy() {
		return $this->merge(['query.order_by' => func_get_args()]);
	}
	
	/**
	 * Sets query row limit
	 * @param int $leftLimit
	 * @param int $rightLimit
	 * @return Manager
	 */
	public function limit($leftLimit, $rightLimit = null) {
		if (isset($rightLimit)) {
			return $this->merge(['query.left_limit' => intval($leftLimit), 'query.right_limit' => intval($rightLimit)]);
		}
		
		return $this->merge(['query.left_limit' => intval($leftLimit)]);
	}
	
	/**
	 * Sets attributes to obtain
	 * @return Manager
	 */
	public function attrs() {
		return $this->merge(['query.attrs' => func_get_args()]);
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