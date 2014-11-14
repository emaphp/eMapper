<?php
namespace eMapper\Query\Fluent;

use eMapper\Query\FluentQuery;
use eMapper\SQL\Configuration\StatementConfiguration;
use eMapper\Query\Predicate\SQLPredicate;
use eMapper\Query\Column;
use eMapper\Query\SQL\FluentTranslator;

class SelectQuery extends AbstractQuery {
	use StatementConfiguration;
	
	/**
	 * Main table
	 * @var string
	 */
	protected $table;
	
	/**
	 * Main table alias
	 * @var string
	 */
	protected $alias;
	
	/**
	 * Columns to fetch
	 * @var array
	 */
	protected $columns;
	
	/**
	 * Condition clauses
	 * @var array
	 */
	protected $whereClauses;
	
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
	 * HAving clause
	 * @var mixed
	 */
	protected $havingClause;
	
	/**
	 * Query arguments
	 * @var \ArrayObject
	 */
	protected $args;
	
	/**
	 * Table map (table -> alias)
	 * @var \ArrayObject
	 */
	protected $tableMapping;
	
	/**
	 * Table joins
	 * @var array
	 */
	protected $joins = [];
	
	/**
	 * Column translator
	 * @var FluentTranslator
	 */
	protected $translator;
	
	public function __construct(FluentQuery $fluent, $table, $alias = null) {
		parent::__construct($fluent);
		$this->table = $table;
		$this->alias = $alias;
		$this->tableMapping = new \ArrayObject([$table => $alias]);
		$this->args = new \ArrayObject();
	}
	
	/**
	 * Sets columns to fetch
	 * @return \eMapper\Query\Fluent\SelectQuery
	 */
	public function select() {
		$this->columns = func_get_args();
		return $this;
	}
	
	/**
	 * Sets conditions to evaluate
	 * @return \eMapper\Query\Fluent\SelectQuery
	 */
	public function where() {
		$this->whereClauses = func_get_args();
		return $this;
	}
	
	/**
	 * Adds an inner join to the current query
	 * @param string $table
	 * @param string|SQLPredicate $alias_or_cond
	 * @param string|SQLPredicate $cond
	 * @return \eMapper\Query\Fluent\SelectQuery
	 */
	public function innerJoin($table, $alias_or_cond, $cond = null) {
		if (is_null($cond)) { //no alias
			$this->joins[$table] = new JoinClause(JoinClause::INNER_JOIN, $table, null, $alias_or_cond);
			$this->tableMapping[$table] = null;
		}
		else {
			$this->joins[$table] = new JoinClause(JoinClause::INNER_JOIN, $table, $alias_or_cond, $cond);
			$this->tableMapping[$table] = $alias_or_cond;
		}
		
		return $this;
	}
	
	/**
	 * Adds a left join to the current query
	 * @param string $table
	 * @param string|SQLPredicate $alias_or_cond
	 * @param string|SQLPredicate $cond
	 * @return \eMapper\Query\Fluent\SelectQuery
	 */
	public function leftJoin($table, $alias_or_cond, $cond = null) {
		if (is_null($cond)) {
			$this->joins[$table] = new JoinClause(JoinClause::LEFT_JOIN, $table, null, $alias_or_cond);
			$this->tableMapping[$table] = null;
		}
		else {
			$this->joins[$table] = new JoinClause(JoinClause::LEFT_JOIN, $table, $alias_or_cond, $cond);
			$this->tableMapping[$table] = $alias_or_cond;
		}
		
		return $this;
	}
	
	/**
	 * Adds a full outer join to the current query
	 * @param string $table
	 * @param string|SQLPredicate $alias_or_cond
	 * @param string|SQLPredicate $cond
	 * @return \eMapper\Query\Fluent\SelectQuery
	 */
	public function fullOuterJoin($table, $alias_or_cond, $cond = null) {
		if (is_null($cond)) {
			$this->joins[$table] = new JoinClause(JoinClause::FULL_OUTER_JOIN, $table, null, $alias_or_cond);
			$this->tableMapping[$table] = null;
		}
		else {
			$this->joins[$table] = new JoinClause(JoinClause::FULL_OUTER_JOIN, $table, $alias_or_cond, $cond);
			$this->tableMapping[$table] = $alias_or_cond;
		}
		
		return $this;
	}
	
	/**
	 * Sets order by clauses
	 * @return \eMapper\Query\Fluent\SelectQuery
	 */
	public function orderBy() {
		$this->orderByClause = func_get_args();
		return $this;
	}
	
	/**
	 * Sets limit clause
	 * @param int $limit
	 * @return \eMapper\Query\Fluent\SelectQuery
	 */
	public function limit($limit) {
		$this->limitClause = intval($limit);
		return $this;	
	}
	
	/**
	 * Sets offset clause
	 * @param int $offset
	 * @return \eMapper\Query\Fluent\SelectQuery
	 */
	public function offset($offset) {
		$this->offsetClause = intval($offset);
		return $this;
	}
	
	/**
	 * Sets group by clauses
	 * @return \eMapper\Query\Fluent\SelectQuery
	 */
	public function groupBy() {
		$this->groupByClause = func_get_args();
		return $this;
	}
	
	/**
	 * Sets the having clause
	 * @param string|SQLPredicate $column
	 * @return \eMapper\Query\Fluent\SelectQuery
	 */
	public function having($column) {
		$this->havingClause = $column;
		return $this;
	}
	
	/**
	 * Retuns a column reference as a string
	 * @param string|Column $column
	 * @return string
	 */
	protected function translateColumn($column) {
		if ($column instanceof Column)
			return $this->translator->translate($column, null, $this->alias);
		elseif (is_string($column) && !empty($column))
			return $column;
		
		throw new \InvalidArgumentException("Columns must be specified either by a Column instance or a non-empty string");
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
				
			if (empty($path)) {
				if (is_null($this->alias))
					return empty($type) ? $column->getName() : $column->getName() . ' ' . $type;
				else
					return empty($type) ? $this->alias . '.' . $column->getName() : $this->alias . '.' . $column->getName() . ' ' . $type;
			}
				
			$references = $column->getPath()[0];
	
			if (!array_key_exists($references, $this->tableMapping))
				throw new \UnexpectedValueException("Column {$column->getName()} references an unknown table '$references'");
	
			if (is_null($this->tableMapping[$references]))
				return empty($type) ? $references . '.' . $column->getName() : $references . '.' . $column->getName() . ' ' . $type;
			else
				return empty($type) ? $this->tableMapping[$references] . '.' . $column->getName() : $this->tableMapping[$references] . '.' . $column->getName() . ' ' . $type;
		}
		else
			return $this->translateColumn($column);
	}
	
	/**
	 * Returns a conditional clause as a string
	 * @param string|SQLPredicate $condition
	 * @throws \InvalidArgumentException
	 * @return string
	 */
	protected function translateCondition($condition) {
		if ($condition instanceof SQLPredicate) {
			return $condition->evaluate($this->translator, $this->fluent->getMapper()->getDriver(), $this->args);
		}
		elseif (is_string($condition) && !empty($condition))
			return $condition;
		else
			throw new \InvalidArgumentException("Conditions must be specified either by a SQLPredicate instance or a non-empty string");
	}
	
	/**
	 * Returns a join expression as a string
	 * @param JoinClause $join
	 * @return string
	 */
	protected function translateJoin(JoinClause $join) {
		$expr = $join->getTypeExpression() . ' ';
		
		if ($join->getAlias())
			$expr .= $join->getTable() . ' ' . $join->getAlias() . ' ON ' . $this->translateCondition($join->getCondition()) . ' ';
		else
			$expr .= $join->getTable() .  ' ON ' . $this->translateCondition($join->getCondition()) . ' ';
		
		return $expr;
	}
	
	/**
	 * Retuns the columns to fetch as a string
	 * @return string
	 */
	protected function getColumnsExpression() {
		if (empty($this->columns))
			return '*';
		
		$columns = [];
		
		foreach($this->columns as $column)
			$columns[] = $this->translateColumn($column);
		
		return implode(',', $columns);
	}
	
	/**
	 * Returns a FROM clause for the current query
	 * @return string
	 */
	protected function getFromClause() {
		$from = '';
		
		if (isset($this->alias))
			$from .= $this->table . ' ' . $this->alias . ' ';
		else
			$from .= $this->table . ' ';
		
		foreach ($this->joins as $join) {
			$from .= $this->translateJoin($join);
		}
		
		return $from;
	}
	
	/**
	 * Retuns a WHERE clause as a string
	 * @throws \UnexpectedValueException
	 * @return string
	 */
	protected function getWhereClause() {
		if (empty($this->whereClauses))
			return '';
		
		$where = [];
		
		foreach ($this->whereClauses as $clause) {
			if ($clause instanceof SQLPredicate) {
				$where[] = $clause->evaluate($this->translator, $this->fluent->getMapper()->getDriver(), $this->args);
			}
			elseif (is_string($clause) && !empty($clause)) {
				$where[] = $clause;
			}
			else
				throw new \UnexpectedValueException("Query conditions must be defined either as a string or as a SQLPredicate instance");
		}
		
		return implode(' ', $where);
	}
	
	/**
	 * Obtains additional clauses as a string
	 * @return string
	 */
	protected function getAdditionalClauses() {
		$clauses = '';
		
		//group by
		if (!empty($this->groupBy)) {
			$group_by = [];
			
			foreach($this->groupBy as $group)
				$group_by[] = $this->translateColumn($group);
			
			$clauses .= 'GROUP BY ' . implode(',', $group_by) . ' ';
			
			//having
			if (isset($this->havingClause)) {
				$having = [];
				
				foreach ($this->havingClause as $have)
					$having[] = $this->translateColumn($have);
				
				$clauses .= 'HAVING ' . implode(',', $having) . ' ';
			}
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
	
	/**
	 * Builds a column translator for the current query
	 */
	protected function buildTranslator() {
		$this->translator = new FluentTranslator($this->tableMapping);
	}
	
	public function build() {
		$this->buildTranslator();
		
		$columns = $this->getColumnsExpression();
		$from = rtrim($this->getFromClause());
		$where = $this->getWhereClause();
		$clauses = $this->getAdditionalClauses();
		
		if (!empty($where)) {
			return rtrim("SELECT $columns FROM $from WHERE $where $clauses");
		}
		
		return rtrim("SELECT $columns FROM $from $clauses");
	}
	
	/**
	 * Fetchs the current query with an optional mapping type
	 * @param string $mapping_type
	 * @return mixed
	 */
	public function fetch($mapping_type = null) {
		$query = $this->build();		
		$mapper = $this->fluent->getMapper();
		
		if (is_null($mapping_type))
			return $mapper->merge($this->config)->query($query);
		
		$config = $this->config;
		$config['map.type'] = $mapping_type;
		return $mapper->merge($config)->query($query);
	}
	
	public function getArguments() {
		return $this->args;
	}
}
?>