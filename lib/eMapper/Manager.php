<?php
namespace eMapper;

use eMapper\Reflection\Profile\ClassProfile;
use eMapper\SQL\Configuration\StatementConfiguration;
use eMapper\Query\Predicate\Filter;
use eMapper\Query\Predicate\SQLPredicate;

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
	 * Returns current query column list
	 * @throws \RuntimeException
	 * @return string
	 */
	protected function getColumns() {
		$columns = '*';
		
		if (array_key_exists('query.columns', $this->config)) {
			if (!empty($this->config['query.columns'])) {
				return implode(', ', $this->config['query.columns']);
			}
		}
		elseif (array_key_exists('query.attrs', $this->config)) {
			if (!empty($this->config['query.attrs'])) {
				$columns = [];
				
				foreach ($this->config['query.attrs'] as $attr) {
					if (!array_key_exists($attr, $this->entity->fieldNames)) {
						throw new \RuntimeException(sprintf("Attribute $attr not declared in class %s", $this->entity->reflectionClass->getName()));
					}
					
					$columns[] = $this->entity->fieldNames[$attr];
				}
				
				return implode(', ', $columns);
			}
		}
		
		return $columns;
	}
	
	/**
	 * Builds ordering and limit clauses
	 * @throws \RuntimeException
	 * @return string
	 */
	protected function getAdditionalClauses() {
		$clauses = [];
		
		//add order
		if (array_key_exists('query.order_by', $this->config)) {
			$order_by = 'ORDER BY';
				
			foreach ($this->config['query.order_by'] as $order) {
				$regex = '/^([\w]+)\s+([ASC|DESC])$/';
		
				if (preg_match($regex, $order, $matches)) {
					if (!array_key_exists($matches[1], $this->fields)) {
						throw new \RuntimeException();
					}
						
					$column = $this->fields[$matches[1]] . ' ' . $matches[2];
				}
				else {
					if (!array_key_exists($order, $this->fields)) {
						throw new \RuntimeException();
					}
						
					$column = $this->fields[$order];
				}
		
				$order_by .= " $column,";
			}
		
			$clauses[] = substr($order_by, 0, -1);
		}
		
		//add limit
		if (array_key_exists('query.left_limit', $this->config)) {
			if (array_key_exists('query.right_limit', $this->config)) {
				$clauses[] = sprintf("LIMIT %d, %d", $this->config['query.left_limit'], $this->config['query.right_limit']);
			}
			else {
				$clauses[] = sprintf("LIMIT %d", $this->config['query.left_limit']);
			}
		}
		
		return implode(' ', $clauses);
	}
	
	/**
	 * Removes manager-only options from configuration values
	 * @param string $values
	 * @return multitype:
	 */
	protected function cleanOptions($values = null) {
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
		$primaryKey = $this->entity->primaryKey;
	
		if (is_null($primaryKey)) {
			throw new \RuntimeException(sprintf("Class %s does not appear to have a primary key", $this->entity->reflectionClass->getName()));
		}
	
		//get columns
		$columns = $this->getColumns();
	
		//get referenced table
		$table = $this->entity->getReferencedTable();
	
		//build condition
		$condition = $this->fields[$primaryKey] . ' = %{0}';
	
		//build config and run query
		$options = array_merge($this->config, ['map.type' => $this->expression]);
		return $this->mapper->merge($options)->query(sprintf("SELECT %s FROM %s WHERE %s", $columns, $table, $condition), $pk);
	}
	
	public function find(SQLPredicate $condition = null) {
		//get referenced table
		$table = $this->entity->getReferencedTable();
		
		//get columns
		$columns = $this->getColumns();
		
		if (array_key_exists('query.distinct', $this->config) && $this->config['query.distinct']) {
			$columns = 'DISTINCT ' . $columns;
		}
		
		//get clauses
		$clauses = $this->getAdditionalClauses();
		
		//get current query options
		$options = $this->cleanOptions(['map.type' => $this->getTypeExpression()]);
		
		if (isset($condition)) {
			$args = [];
			$condition = $condition->evaluate($this->entity, $args);
			return $this->mapper->merge($options)->query(sprintf("SELECT %s WHERE %s FROM %s %s", $columns, $condition, $table, $clauses), $args);
		}
		elseif (array_key_exists('query.filter', $this->config) && !empty($this->config['query.filter'])) {
			$args = [];
			$filters = [];
			
			foreach ($this->config['query.filter'] as $filter) {
				$filters[] = $filter->evaluate($this->entity, $args);
			}
			
			$condition = implode(' AND ', $filters);
			
			return $this->mapper->merge($options)->query(sprintf("SELECT %s WHERE %s FROM %s %s", $columns, $condition, $table, $clauses), $args);
		}
		else {
			return $this->mapper->merge($options)->query(sprintf("SELECT %s FROM %s %s", $columns, $table, $clauses));
		}
	}
	
	/**
	 * Returns the first result from a query 
	 * @param SQLPredicate $condition
	 * @return NULL|object
	 */
	public function get(SQLPredicate $condition = null) {
		$result = $this->find($condition);
		
		if (empty($result)) {
			return null;
		}
		
		return current($result);
	}
	
	/**
	 * Stores an instance into the database
	 * @param object $entity
	 * @throws \RuntimeException
	 * @return boolean
	 */
	public function save($entity) {
		$primaryKey = $this->entity->primaryKey;
		
		if (is_null($primaryKey)) {
			throw new \RuntimeException(sprintf("Class %s does not appear to have a primary key", $this->entity->reflectionClass->getName()));
		}
		
		//get table
		$table = $this->entity->getReferencedTable();
		
		//get primary key value
		$pkProperty = $this->entity->propertiesConfig[$primaryKey]->reflectionProperty;
		$pk = $pkProperty->getValue($entity);
		
		if (is_null($pk)) {
			//insert new entity
			$fields = implode(',', $fields);
			
			//create insertion expression
			$expressions = [];
			
			foreach (array_keys($fields) as $property) {
				$expressions[] = '#{' . $property . '}';
			}
			
			$expressions = implode(',', $expressions);
			
			return $this->mapper->query(sprintf("INSERT INTO %s (%s) VALUES (%s)", $table, $fields, $expressions), $entity);
		}
		
		//build values expression
		$expressions = [];
		
		foreach ($fields as $property => $column) {
			$expressions = $column . ' = #{' . $property . '}';
		}
		
		//build values list
		$values = implode(',', $expressions);
		
		//build condition
		$condition = $this->fields[$primaryKey] . ' = %{1}';
		
		//update entity
		return $this->mapper->query(sprintf("UPDATE %s SET %s WHERE %s", $table, $values, $condition), $entity, $pk);
	}
	
	/**
	 * Removes given entity from database
	 * @param object $entity
	 * @throws \RuntimeException
	 * @return boolean
	 */
	public function delete($entity) {
		//get table
		$table = $this->entity->getReferencedTable();
		
		if ($entity instanceof SQLPredicate) {
			$args = [];
			
			//evaluate condition
			$condition = $entity->evaluate($this->entity, $args);
			
			//delete entity
			return $this->mapper->query(sprintf("DELETE FROM %s WHERE %s", $table, $condition), $args);
		}
		else {
			$primaryKey = $this->entity->primaryKey;
			
			if (is_null($primaryKey)) {
				throw new \RuntimeException(sprintf("Class %s does not appear to have a primary key", $this->entity->reflectionClass->getName()));
			}
			
			//get primary key value
			$pkProperty = $this->entity->propertiesConfig[$primaryKey]->reflectionProperty;
			$pk = $pkProperty->getValue($entity);
			
			//delete entity
			return $this->mapper->query(sprintf("DELETE FROM %s WHERE %s", $table, $this->fields[$pkProperty] . ' = %{0}'), $pk);
		}
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