<?php
namespace eMapper;

use eMapper\Reflection\Profile\ClassProfile;
use eMapper\Reflection\PropertyAccessor;
use eMapper\SQL\Configuration\StatementConfiguration;
use eMapper\Reflection\EntityMapper;
use eMapper\Query\Predicate\Filter;
use eMapper\Query\Predicate\SQLPredicate;
use eMapper\Query\Builder\InsertQueryBuilder;
use eMapper\Query\Builder\UpdateQueryBuilder;
use eMapper\Query\Builder\SelectQueryBuilder;
use eMapper\Query\Builder\DeleteQueryBuilder;
use eMapper\Query\Attr;
use eMapper\Query\Field;
use eMapper\Query\Column;
use eMapper\Query\Aggregate\SQLCount;
use eMapper\Query\Aggregate\SQLFunction;
use eMapper\Query\Aggregate\SQLAverage;
use eMapper\Query\Aggregate\SQLMax;
use eMapper\Query\Aggregate\SQLMin;
use eMapper\Query\Aggregate\SQLSum;
use eMapper\Reflection\Profile\Association\OneToMany;
use eMapper\Reflection\Profile\Association\OneToOne;
use eMapper\Query\Q;

/**
 * The Manager class provides a common interface for obtaining data related to an entity.
 * @author emaphp
 */
class Manager {
	use StatementConfiguration;
	use EntityMapper;
	use PropertyAccessor;
	
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
	protected function getListMappingExpression() {
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
		if (is_array($values))
			return array_merge($clean, $values);
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
		$primaryKey = $this->entity->getPrimaryKey();
	
		if (is_null($primaryKey))
			throw new \RuntimeException(sprintf("Class %s does not appear to have a primary key", $this->entity->reflectionClass->getName()));
	
		//build query
		$query = new SelectQueryBuilder($this->entity);
		$query->setCondition(Attr::__callstatic($primaryKey)->eq($pk));
		list($query, $args) = $query->build($this->mapper->getDriver(), $this->config);
		
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
		list($query, $args) = $query->build($this->mapper->getDriver(), $this->config);
		
		//run query
		$options = $this->clean_options(['map.type' => $this->getListMappingExpression()]);
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
		list($query, $args) = $query->build($this->mapper->getDriver(), $this->config);
		
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
		
		if (!is_string($statementId) || empty($statementId))
			$this->driver->throw_exception("Statement id is not a valid string");
		
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
		
		if ($stmt === false)
			$this->mapper->driver->throw_exception("Statement '$statementId' could not be found");
		
		//get statement config
		$query = $stmt->getQuery();
		$options = is_null($stmt->getOptions()) ? [] : $stmt->getOptions()->getConfig();
		
		//add query to method parameters
		array_unshift($args, $query);
		
		//merge options
		$options = array_merge($options, $this->clean_options());
		return call_user_func_array([$this->mapper->merge($options), 'query'], $args);
	}
	
	/**
	 * Stores an instance into the database
	 * @param object $entity
	 * @throws \RuntimeException
	 * @return boolean|integer
	 */
	public function save(&$entity, $depth = 1) {
		if (is_null($entity)) {
			return null;
		}
		
		//connect to database
		$this->mapper->connect();
		
		//begin transaction
		$this->mapper->beginTransaction();
		
		if ($depth == 0) {
			//get primary key
			$pk = $this->getPropertyValue($this->entity, $entity, $this->entity->getPrimaryKey());
			
			if (is_null($pk)) {
				//check for duplicated values
				$checks = $this->entity->getDuplicateChecks();
				
				if (!empty($checks)) {
					$ignore = true;
					
					foreach ($checks as $attr) {
						$attribute = $this->entity->getProperty($attr);
						$attrValue = $this->getPropertyValue($this->entity, $entity, $attr);
						$pk = $this->mapper->type('int')->query("SELECT %s FROM @@%s WHERE %s = %s",
																$this->entity->getPrimaryKeyProperty()->getColumn(),
																$this->entity->getReferredTable(),
																$attribute->getColumn(), $attrValue);
						
						if (!is_null($pk)) {
							$ignore = $attribute->getCheckDuplicate() == 'ignore';
							break;
						}
					}
					
					if (!is_null($pk)) {
						$this->setPropertyValue($this->entity, $entity, $this->entity->getPrimaryKey(), $pk);
						return $ignore ? $pk : $this->save($entity, $depth);
					}
				}
				
				//build insert query
				$query = new InsertQueryBuilder($this->entity);
				list($query, $_) = $query->build($this->mapper->getDriver());
				$this->mapper->sql($query, $entity);
				$pk = $this->mapper->lastId();
					
				//set primary key value
				$this->setPropertyValue($this->entity, $entity, $this->entity->getPrimaryKey(), $pk);
				$this->mapper->commit();
				return $pk;
			}
			
			//build update query
			$query = new UpdateQueryBuilder($this->entity);
			$query->setCondition(Attr::__callstatic($this->entity->getPrimaryKey())->eq($pk));
			list($query, $args) = $query->build($this->mapper->getDriver());
			$this->mapper->sql($query, $entity, $args);
			$this->mapper->commit();
			return $pk;
		}
		
		$foreignKeys = [];
		
		//store parent object, if any
		if ($this->entity->hasForeignKeys()) {
			//try storing related entities first
			$foreignKeys = $this->entity->getForeignKeys();
			
			foreach ($foreignKeys as $key => $value) {
				$assoc = $this->entity->getAssociation($value);
				
				//don't save read-only values
				if ($assoc->isReadOnly())
					continue;
				
				$related = $this->getAssociationValue($this->entity, $entity, $assoc);
				
				if (is_null($related))
					$this->setPropertyValue($this->entity, $entity, $key, null); //set attribute to NULL
				else {
					$id = $assoc->save($this->mapper, $entity, $related, $depth - 1);
				
					if (!is_null($id))
						$this->setPropertyValue($this->entity, $entity, $key, $id);
				}				
			}
		}
		
		//get primary key value
		$pk = $this->getPropertyValue($this->entity, $entity, $this->entity->getPrimaryKey());
			
		if (is_null($pk)) {
			//check for duplicated values
			$checks = $this->entity->getDuplicateChecks();
			
			if (!empty($checks)) {
				$ignore = true;
				
				foreach ($checks as $attr) {
					$attribute = $this->entity->getProperty($attr);
					$attrValue = $this->getPropertyValue($this->entity, $entity, $attr);
					$pk = $this->mapper->type('int')->query("SELECT %s FROM @@%s WHERE %s = %s",
															$this->entity->getPrimaryKeyProperty()->getColumn(),
															$this->entity->getReferredTable(),
															$attribute->getColumn(), $attrValue);
					
					if (!is_null($pk)) {
						$ignore = $attribute->getCheckDuplicate() == 'ignore';
						break;
					}
				}
				
				if (!is_null($pk)) {
					$this->setPropertyValue($this->entity, $entity, $this->entity->getPrimaryKey(), $pk);
					return $ignore ? $pk : $this->save($entity, $depth);
				}
			}
			
			//build insert query
			$query = new InsertQueryBuilder($this->entity);
			list($query, $_) = $query->build($this->mapper->getDriver());
			$this->mapper->sql($query, $entity);
			$pk = $this->mapper->lastId();
				
			//set primary key value
			$this->setPropertyValue($this->entity, $entity, $this->entity->getPrimaryKey(), $pk);
		}
		else {
			//build update query
			$query = new UpdateQueryBuilder($this->entity);
			$query->setCondition(Attr::__callstatic($this->entity->getPrimaryKey())->eq($pk));
			list($query, $args) = $query->build($this->mapper->getDriver());
			$this->mapper->sql($query, $entity, $args);
		}
		
		foreach ($this->entity->getAssociations() as $name => $association) {
			if (in_array($name, $foreignKeys))//already persisted
				continue;
			
			if ($association->isReadOnly()) //don't save read-only values
				continue;
			
			//obtain associated value
			$value = $this->getAssociationValue($this->entity, $entity, $association);
			
			if (!is_null($value))
				$association->save($this->mapper, $entity, $value, $depth - 1);
		}
		
		$this->mapper->commit();
		return $pk;
	}
	
	/**
	 * Removes given entity from database
	 * @param object $entity
	 * @throws \RuntimeException
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
		$pk = $this->getPropertyValue($this->entity, $entity, $this->entity->getPrimaryKey());

		//determine if related data must be eliminated as well		
		foreach ($this->entity->getReferences() as $name) {
			$assoc = $this->entity->getAssociation($name);
			$assoc->delete($this->mapper, $pk);
		}
		
		//build query
		$query = new DeleteQueryBuilder($this->entity);
		$condition = Attr::__callStatic($this->entity->getPrimaryKey())->eq($pk);
		$query->setCondition($condition);
		list($query, $args) = $query->build($this->mapper->getDriver());
		
		//run query
		$result = $this->mapper->query($query, $args);
		
		//commit
		$this->mapper->commit();
		return $result;
	}
	
	/**
	 * Removes a set of entities by a given condition
	 * @param SQLPredicate $condition
	 * @return boolean
	 */
	public function deleteWhere(SQLPredicate $condition = null, $cascade = false) {
		//connect to database
		$this->mapper->connect();
		
		//begin transaction
		$this->mapper->beginTransaction();
		
		if ($cascade) {
			$result = $this->find($condition);
			
			foreach ($list as $entity)
				$this->delete($entity);
		}
		else {
			//build query
			$query = new DeleteQueryBuilder($this->entity);
			$query->setCondition($condition);
			list($query, $args) = $query->build($this->mapper->getDriver(), $this->config);
			
			//run query
			$result = $this->mapper->query($query, $args);
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
		$query = new DeleteQueryBuilder($this->entity, true);
		list($query, $_) = $query->build($this->mapper->getDriver());
		$result = $this->mapper->query($query);
		
		//commit
		$this->mapper->commit();
		return $result;
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
		list($query, $args) = $query->build($this->mapper->getDriver(), $this->config);
		
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
	public function index(Field $index = null) {
		if (is_null($index)) return $this->discard('query.index');
		elseif ($index instanceof Attr) {
			//get custom type (if any)
			$type = $index->getType();
			
			if (isset($type)) return $this->merge(['query.index' => $index->getName() . ':' . $type]);
			return $this->merge(['query.index' => $index->getName()]);
		}
		elseif ($index instanceof Column) {
			//check if the column is associated to a property
			$columns = $this->entity->getColumnNames();
			
			if (!array_key_exists($index->getName(), $columns))
				throw new \InvalidArgumentException(sprintf("Cannot index by non declared column %s in class %s", $index->getName(), $this->entity->getReflectionClass()->getName()));
			
			//get property name
			$property = $columns[$index->getName()];
			
			//obtain custom type
			$type = $index->getType();
			if (isset($type)) return $this->merge(['query.index' => $property . ':' . $type]);
			return $this->merge(['query.index' => $property]);
		}
		
		throw new \InvalidArgumentException("Index must be specified as valid Field instance");
	}
	
	/**
	 * Sets grouping column/callback
	 * @param Field $group
	 * @return Manager
	 */
	public function group(Field $group = null) {
		if (is_null($group)) return $this->discard('query.group');
		elseif ($group instanceof Attr) {
			//get custom type (if any)
			$type = $group->getType();
			
			if (isset($type)) return $this->merge(['query.group' => $group->getName() . ':' . $type]);		
			return $this->merge(['query.group' => $group->getName()]);
		}
		elseif ($group instanceof Column) {
			//check if the column is associated to a property
			$columns = $this->entity->getColumnNames();
				
			if (!array_key_exists($group->getName(), $columns))
				throw new \InvalidArgumentException(sprintf("Cannot index by non declared column %s in class %s", $group->getName(), $this->entity->getReflectionClass()->getName()));
			
			$property = $columns[$group->getName()];
			$type = $group->getType();
			
			if (isset($type)) return $this->merge(['query.group' => $property . ':' . $type]);		
			return $this->merge(['query.group' => $property]);
		}
		
		throw new \InvalidArgumentException("Group must be specified as a valid Field instance");
	}
	
	/**
	 * Sets query order
	 * @return Manager
	 */
	public function order_by($order) {
		if (is_null($order))
			return $this->discard('query.order');	
		return $this->merge(['query.order' => func_get_args()]);
	}
	
	/**
	 * Sets query row limit
	 * @param int $from
	 * @param int $to
	 * @return Manager
	 */
	public function limit($from, $to = null) {
		if (is_null($from)) return $this->discard('query.from', 'query.to');
		if (isset($to)) return $this->merge(['query.from' => intval($from), 'query.to' => intval($to)]);
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
	 * Filters results by a condition (AND operator)
	 * @return Manager
	 */
	public function filter() {
		return $this->append('query.filter', new Filter(func_get_args()));
	}
	
	/**
	 * Excludes results by a condition (AND operator)
	 * @return Manager
	 */
	public function exclude() {
		return $this->append('query.filter', new Filter(func_get_args(), true));
	}
	
	/**
	 * Filters results by a condition (OR operator)
	 * @return Manager
	 */
	public function where() {
		return $this->append('query.filter', new Filter(func_get_args(), false, Q::LOGICAL_OR));
	}
	
	/**
	 * Excludes results by a condition (OR operator)
	 * @return Manager
	 */
	public function where_not() {
		return $this->append('query.filter', new Filter(func_get_args(), true, Q::LOGICAL_OR));
	}
	
	/**
	 * Defines if current query uses the distinct clause
	 * @return Manager
	 */
	public function distinct($boolean = true) {
		return $this->merge(['query.distinct' => $boolean]);
	}
}
?>
