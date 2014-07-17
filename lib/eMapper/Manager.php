<?php
namespace eMapper;

use eMapper\Reflection\Profile\ClassProfile;
use eMapper\SQL\Configuration\StatementConfiguration;
use eMapper\Query\Predicate\Filter;
use eMapper\Query\Predicate\SQLPredicate;
use eMapper\Query\Builder\DeleteQueryBuilder;
use eMapper\Query\Attr;
use eMapper\Query\Builder\InsertQueryBuilder;
use eMapper\Query\Builder\UpdateQueryBuilder;
use eMapper\Query\Builder\SelectQueryBuilder;
use eMapper\Query\Field;
use eMapper\Query\Column;

class Manager {
	use StatementConfiguration {
		index as index_callback;
		group as group_callback;
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
	
	/**
	 * Sets indexation column/callback
	 * @param mixed $index
	 * @return Manager
	 */
	public function index($index) {
		if ($index instanceof \Closure) {
			return $this->index_callback($index);
		}
		elseif ($index instanceof Attr) {
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
		
		throw new \InvalidArgumentException("Index must be specified as a Field or Closure instance");
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
		elseif ($group instanceof Attr) {
			$type = $index->getType();
				
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
		
		throw new \InvalidArgumentException("Group must be specified as a Attr or Closure instance");
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