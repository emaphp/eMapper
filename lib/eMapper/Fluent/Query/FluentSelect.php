<?php
namespace eMapper\Fluent\Query;

use eMapper\SQL\Predicate\SQLPredicate;
use eMapper\Fluent\Query\Clause\HavingClause;
use eMapper\Fluent\Query\Clause\JoinClause;
use eMapper\Query\Column;
use eMapper\Query\Func;
use eMapper\Query\Field;
use eMapper\Query\Schema;

/**
 * The FluentSelect class provides a fluent interface for building SELECT queries
 * @author emaphp
 */
class FluentSelect extends AbstractQuery {
	/**
	 * Columns to fetch
	 * @var array
	 */
	protected $columns;
	
	/**
	 * Join list
	 * @var array
	 */
	protected $joins = [];

	/**
	 * Order clauses
	 * @var array
	 */
	protected $orderByClause;
	
	/**
	 * Row limit
	 * @var int
	 */
	protected $limitClause;
	
	/**
	 * Row offset
	 * @var int
	 */
	protected $offsetClause;
	
	/**
	 * Group by clauses
	 * @var array
	 */
	protected $groupByClause;
	
	/**
	 * Having clause
	 * @var HavingClause
	 */
	protected $havingClause;	
	
	/**
	 * Sets columns to fetch
	 * @return \eMapper\Fluent\Query\FluentSelect
	 */
	public function select($columns) {
		if (is_array($columns)) {
			$this->columns = $columns;
			return $this;
		}
		
		$this->columns = func_get_args();
		return $this;
	}
	
	/**
	 * Sets order by clauses
	 * @return \eMapper\Fluent\Query\FluentSelect
	 */
	public function orderBy($orderBy = null) {
		if (is_array($orderBy))
			$this->orderByClause = $orderBy;
		else
			$this->orderByClause = func_get_args();
		return $this;
	}
	
	/**
	 * Sets limit clause
	 * @param int $limit
	 * @return \eMapper\Fluent\Query\FluentSelect
	 */
	public function limit($limit) {
		$this->limitClause = intval($limit);
		return $this;	
	}
	
	/**
	 * Sets offset clause
	 * @param int $offset
	 * @return \eMapper\Fluent\Query\FluentSelect
	 */
	public function offset($offset) {
		$this->offsetClause = intval($offset);
		return $this;
	}
	
	/**
	 * Sets group by clauses
	 * @return \eMapper\Fluent\Query\FluentSelect
	 */
	public function groupBy($groupBy) {
		if (is_array($groupBy))
			$this->groupByClause = $groupBy;
		else
			$this->groupByClause = func_get_args();
		return $this;
	}

	/**
	 * Sets the having clause
	 * @param string|SQLPredicate $having
	 * @return \eMapper\SQL\Fluent\FluentSelect
	 */
	public function having($having) {
		$this->havingClause = new HavingClause($this->driver, func_get_args());
		return $this;
	}
	
	public function build() {
		//create query schema
		$schema = new Schema($this->getFluent()->getEntityProfile());
		
		//columns
		if (!empty($this->columns)) {
			$columns = [];
			foreach ($this->columns as $column) {
				if ($column instanceof Field)
					$columns[] = $schema->translate($column, $this->alias, function ($column, $field) {
						$alias = $field->getColumnAlias();
						if (empty($alias))
							return $column;
						return $column . ' AS ' . $alias;
					});
				elseif (is_string($column))
					$columns[] = $column;
			}
			$select = implode(',', $columns);
		}
		else
			$select = '*';
		
		//WHERE clause
		$where = '';
		if (isset($this->whereClause))
			$where = $this->whereClause->build($schema);
		
		//additional clauses
		$clauses = '';
		
		//GROUP BY
		if (!empty($this->groupByClause)) {
			$groups = [];
			foreach ($this->groupByClause as $group) {
				if (is_string($group))
					$groups[] = $group;
				elseif ($group instanceof Field)
					$groups[] = $schema->translate($group, $this->alias);
				else
					throw new \InvalidArgumentException("Groups must be specified using a string or a Field instance");
			}
			$clauses .= " GROUP BY " . implode(',', $groups);
			
			if (isset($this->havingClause))
				$clauses .= " HAVING " . $this->havingClause->build($schema);
		}
		
		//ORDER BY
		if (!empty($this->orderByClause)) {
			$order = [];
			foreach ($this->orderByClause as $field) {
				if (is_string($field))
					$order[] = $field;
				elseif ($field instanceof Field)
					$order[] = $schema->translate($field, $this->alias, function ($column, $field) {
						$type = $field->getType();
						if (!empty($type) && (strtolower($type) == 'asc' || strtolower($type) == 'desc'))
							return "$column $type";						
						return $column;
					});
				else
					throw new \InvalidArgumentException("Order must be specified using a string or a Field instance");
			}
			$clauses .= (" ORDER BY " . implode(',', $order));
		}
		
		//limit
		if (isset($this->limitClause))
			$clauses .= " LIMIT {$this->limitClause}";
		
		//offset
		if (isset($this->offsetClause))
			$clauses .= " OFFSET {$this->offsetClause}";
		
		//update schema
		$this->updateSchema($schema);
		
		//FROM clause
		$from = rtrim($this->fromClause->build($this, $schema));
		
		//build query
		$query = "SELECT $select FROM $from";
		if (!empty($where))
			$query .= rtrim(" WHERE " . $where);
		if (!empty($clauses))
			$query .= rtrim($clauses);
		
		//generate query arguments
		$args = [];

		if (isset($this->whereClause) && $this->whereClause->hasArguments())
			$args = $this->whereClause->getArguments();
		
		if (!empty($this->groupByClause) && isset($this->havingClause) && $this->havingClause->hasArguments())
			$args = array_merge($args, $this->havingClause->getArguments());

		//get generated arguments
		$complexArg = $schema->getArguments();
		
		//append complexArg to argument list if necessary
		if (!empty($complexArg))
			array_unshift($args, $complexArg);
		
		return [$query, $args];
	}
	
	/**
	 * Fetchs the current query with an optional mapping type
	 * @param string $mappingType
	 * @return mixed
	 */
	public function fetch($mappingType = null) {
		list($query, $args) = $this->build();

		//generate a mapper instance
		if (is_null($mappingType))
			$mapper = empty($this->config) ? $this->fluent->getMapper() : $this->fluent->getMapper()->merge($this->config);
		else {
			$config = empty($this->config) ? ['map.type' => $mappingType] : array_merge($this->config, ['map.type' => $mappingType]);
			$mapper = $this->fluent->getMapper()->merge($config);
		}
				
		if (empty($args))
			return $mapper->query($query);
		
		//append query to argument list
		return $mapper->execute($query, $args);
	}
}