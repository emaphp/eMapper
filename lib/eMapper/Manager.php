<?php
namespace eMapper;

use eMapper\Reflection\Profile\ClassProfile;
use eMapper\SQL\Configuration\StatementConfiguration;

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
	
	/**
	 * Entity fields
	 * @var array
	 */
	protected $fields;
	
	public function __construct(Mapper $mapper, ClassProfile $entity) {
		$this->mapper = $mapper;
		$this->entity = $entity;
		
		//default mapping expression
		$this->expression = 'obj:' . $entity->reflectionClass->getName();
		
		//entity fields
		$this->fields = $entity->getFieldNames();
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
			throw new \RuntimeException("Class does not appear to have a primary key");
		}
		
		//get referenced table
		$table = $this->entity->getReferencedTable();
		
		//build condition
		$condition = $this->fields[$primaryKey] . ' = %{0}';
		
		//build config and run query
		$options = array_merge($this->config, ['map.type' => $this->expression]);
		return $this->mapper->merge($options)->query(sprintf("SELECT * FROM %s WHERE %s", $table, $condition), $pk);
	}
	
	/**
	 * Obtains all entities
	 * @throws \RuntimeException
	 * @return array
	 */
	public function findAll() {
		//get referenced table
		$table = $this->entity->getReferencedTable();
		
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
		
		$expressions = [];
		
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

			$expressions[] = substr($order_by, 0, -1);
		}
		
		//add limit
		if (array_key_exists('query.left_limit', $this->config)) {
			if (array_key_exists('query.right_limit', $this->config)) {
				$expressions[] = sprintf("LIMIT %d, %d", $this->config['query.left_limit'], $this->config['query.right_limit']);
			}
			else {
				$expressions[] = sprintf("LIMIT %d", $this->config['query.left_limit']);
			}
		}
		
		$options = array_merge($this->config, ['map.type' => $mappingExpression]);
		return $this->mapper->merge($options)->query(sprintf("SELECT * FROM %s %s", $table, implode(' ', $expressions)));
	}
	
	public function find() {
		
	}
	
	public function get() {
		
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
			throw new \RuntimeException("Class does not appear to have a primary key");
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
		$primaryKey = $this->entity->primaryKey;
		
		if (is_null($primaryKey)) {
			throw new \RuntimeException("Class does not appear to have a primary key");
		}
		
		//get table
		$table = $this->entity->getReferencedTable();
		
		//get primary key value
		$pkProperty = $this->entity->propertiesConfig[$primaryKey]->reflectionProperty;
		$pk = $pkProperty->getValue($entity);
		
		//delete entity
		return $this->mapper->query(sprintf("DELETE FROM %s WHERE %s", $table, $this->fields[$pkProperty] . ' = %{0}'), $pk);
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
}
?>