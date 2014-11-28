<?php
namespace eMapper\SQL\Fluent;

use eMapper\SQL\Predicate\SQLPredicate;
use eMapper\Query\Column;
use eMapper\SQL\Field\FluentFieldTranslator;
use eMapper\SQL\Fluent\Clause\HavingClause;

/**
 * The FluentSelect class provides a fluent interface for building SELECT queries
 * @author emaphp
 */
class FluentSelect extends AbstractFluentQuery {	
	/**
	 * Columns to fetch
	 * @var array
	 */
	protected $columns;

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
	 * @return \eMapper\SQL\Fluent\FluentSelect
	 */
	public function select() {
		$this->columns = func_get_args();
		return $this;
	}
	
	/**
	 * Sets order by clauses
	 * @return \eMapper\SQL\Fluent\FluentSelect
	 */
	public function orderBy() {
		$this->orderByClause = func_get_args();
		return $this;
	}
	
	/**
	 * Sets limit clause
	 * @param int $limit
	 * @return \eMapper\SQL\Fluent\FluentSelect
	 */
	public function limit($limit) {
		$this->limitClause = intval($limit);
		return $this;	
	}
	
	/**
	 * Sets offset clause
	 * @param int $offset
	 * @return \eMapper\SQL\Fluent\FluentSelect
	 */
	public function offset($offset) {
		$this->offsetClause = intval($offset);
		return $this;
	}
	
	/**
	 * Sets group by clauses
	 * @return \eMapper\SQL\Fluent\FluentSelect
	 */
	public function groupBy() {
		$this->groupByClause = func_get_args();
		return $this;
	}
	
	/*
	 * HAVING
	 */
	
	/**
	 * Returns a HAVING clause as a string
	 * @return string
	 */
	protected function buildHavingClause() {
		if (isset($this->havingClause)) {
			return $this->havingClause->build($this->translator, $this->fluent->getMapper()->getDriver());
		}
		
		return '';
	}
	
	/**
	 * Sets the having clause
	 * @param string|SQLPredicate $having
	 * @return \eMapper\SQL\Fluent\FluentSelect
	 */
	public function having($having) {
		$this->havingClause = new HavingClause(func_get_args());
		return $this;
	}
	
	/**
	 * Retuns a column reference as a string
	 * @param string|Column $column
	 * @return string
	 */
	protected function translateColumn($column) {
		if ($column instanceof Column) {
			return $this->translator->translate($column, $this->fromClause->getAlias());
		}
		elseif (is_string($column) && !empty($column))
			return $column;
		
		throw new \InvalidArgumentException("Columns must be specified either by a Column instance or a non-empty string");
	}
	
	/**
	 * Obtains a select column as a string
	 * @param mixed $column
	 * @throws \UnexpectedValueException
	 * @return string
	 */
	protected function translateSelectColumn($column) {
		if ($column instanceof Column) {
			$path = $column->getPath();
			$name = $column->getType();
			$alias = $this->fromClause->getAlias();
			$tableList = $this->fromClause->getTableList();
		
			if (empty($path)) {
				if (is_null($alias))
					return empty($name) ? $column->getName() : $column->getName() . ' AS ' . $name;
				else
					return empty($name) ? $alias . '.' . $column->getName() : $alias . '.' . $column->getName() . ' AS ' . $name;
			}
		
			$references = $column->getPath()[0];
		
			if (!array_key_exists($references, $tableList))
				throw new \UnexpectedValueException("Column {$column->getName()} references an unknown table '$references'");
		
			return empty($name) ? $references . '.' . $column->getName() : $references . '.' . $column->getName() . ' AS ' . $name;
		}
		else
			return $this->translateColumn($column);
	}
	
	/**
	 * Obtains an order column as a string
	 * @param string|Column $column
	 * @throws \UnexpectedValueException
	 * @return string
	 */
	protected function translateOrderColumn($column) {
		if ($column instanceof Column) {
			$path = $column->getPath();
			$type = $column->getType();
			$alias = $this->fromClause->getAlias();
			$tableList = $this->fromClause->getTableList();
				
			if (empty($path)) {
				if (is_null($alias))
					return empty($type) ? $column->getName() : $column->getName() . ' ' . $type;
				else
					return empty($type) ? $alias . '.' . $column->getName() : $alias . '.' . $column->getName() . ' ' . $type;
			}
				
			$references = $column->getPath()[0];
	
			if (!array_key_exists($references, $tableList))
				throw new \UnexpectedValueException("Column {$column->getName()} references an unknown table '$references'");
	
			return empty($type) ? $references . '.' . $column->getName() : $references . '.' . $column->getName() . ' ' . $type;
		}
		else
			return $this->translateColumn($column);
	}
	
	/**
	 * Retuns the columns to fetch as a string
	 * @return string
	 */
	protected function buildSelectClause() {
		if (empty($this->columns))
			return '*';
		
		$columns = [];
		
		foreach($this->columns as $column)
			$columns[] = $this->translateSelectColumn($column);
		
		return implode(',', $columns);
	}
		
	/**
	 * Obtains additional clauses as a string
	 * @return string
	 */
	protected function buildAdditionalClauses() {
		$clauses = '';
		
		//group by
		if (!empty($this->groupBy)) {
			$group_by = [];
			
			foreach($this->groupBy as $group)
				$group_by[] = $this->translateColumn($group);
			
			$clauses .= 'GROUP BY ' . implode(',', $group_by) . ' ';
			
			//add having clause
			$clauses .= $this->buildHavingClause();
		}
		
		//order by
		if (!empty($this->orderByClause)) {
			$order_by = [];
			
			foreach($this->orderByClause as $order)
				$order_by[] = $this->translateOrderColumn($order);
			
			$clauses .= 'ORDER BY ' . implode(',', $order_by) . ' ';
		}
		
		//limit
		if (isset($this->limitClause))
			$clauses .= "LIMIT {$this->limitClause} ";
		
		//offset
		if (isset($this->offsetClause))
			$clauses .= "OFFSET {$this->offsetClause}";
		
		return $clauses;
	}
	
	public function build() {
		//FROM clause
		$from = rtrim($this->fromClause->build());
		$fromArgs = $this->fromClause->getArguments();
		
		//create field translator from joined tables
		$this->translator = new FluentFieldTranslator($this->fromClause->getTableList());
		
		//SELECT clause
		$columns = $this->buildSelectClause();

		//WHERE clause
		$where = rtrim($this->buildWhereClause());
		
		//etc...
		$clauses = $this->buildAdditionalClauses();
		
		//build query structure
		$query = empty($where) ? rtrim("SELECT $columns FROM $from $clauses") : rtrim("SELECT $columns FROM $from WHERE $where $clauses"); 
		
		//generate query arguments
		$args = [];
		$counter = 0;
		$complexArg = !empty($fromArgs) ? $fromArgs : [];

		//append arguments in WHERE clause
		if (isset($this->whereClause)) {
			$whereArgs = $this->whereClause->getArguments();
			
			if ($this->whereClause->getClause() instanceof SQLPredicate)
				$complexArg = array_merge($whereArgs, $complexArg);
			elseif (!empty($whereArgs)) {
				foreach ($whereArgs as $arg)
					$args[$counter++] = $arg;
			}
		}
		
		//append arguments in HAVING clause
		if (isset($this->havingClause)) {
			$havingArgs = $this->havingClause->getArguments();
			
			if ($this->havingClause->getClause() instanceof SQLPredicate)
				$complexArg = array_merge($havingArgs, $complexArg);
			elseif (!empty($havingArgs)) {
				foreach ($havingArgs as $arg)
					$args[$counter++] = $arg;
			}
		}
		
		//append complexArg to argument list if necessary
		if (!empty($complexArg))
			array_unshift($args, $complexArg);
		
		return [$query, $args];
	}
	
	/**
	 * Fetchs the current query with an optional mapping type
	 * @param string $mapping_type
	 * @return mixed
	 */
	public function fetch($mapping_type = null) {
		list($query, $args) = $this->build();

		//generate a mapper instance
		if (is_null($mapping_type)) {
			$mapper = empty($this->config) ? $this->fluent->getMapper() : $this->fluent->getMapper()->merge($this->config);
		}
		else {
			$config = empty($this->config) ? ['map.type' => $mapping_type] : array_merge($this->config, ['map.type' => $mapping_type]);
			$mapper = $this->fluent->getMapper()->merge($config);
		}
				
		if (empty($args))
			return $mapper->query($query);
		
		//append query to argument list
		array_unshift($args, $query);
		return call_user_func_array([$mapper, 'query'], $args);
	}
}
?>