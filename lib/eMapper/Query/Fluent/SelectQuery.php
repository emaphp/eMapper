<?php
namespace eMapper\Query\Fluent;

use eMapper\Query\FluentQuery;
use eMapper\SQL\Configuration\StatementConfiguration;
use eMapper\Query\Predicate\SQLPredicate;
use eMapper\Query\Column;

class SelectQuery extends AbstractQuery {
	use StatementConfiguration;
	
	protected $query;
	protected $table;
	protected $alias;
	protected $columns = [];
	protected $whereClauses = [];
	protected $orderBy;
	protected $limitClause;
	protected $offsetClause;
	protected $groupBy;
	protected $havingClause;
	
	protected $tableMapping = [];
	
	public function __construct(FluentQuery $query, $table, $alias) {
		parent::__construct($query);
		$this->table = $table;
		$this->alias = $alias;
	}
	
	public function select() {
		$this->columns = func_get_args();
		return $this;
	}
	
	public function where() {
		$this->whereClauses = func_get_args();
		return $this;
	}
	
	public function innerJoin($table, $alias_or_cond, $cond = null) {
		if (is_null($cond))
			$this->joins[] = new JoinClause(JoinClause::INNER_JOIN, $table, null, $alias_or_cond);
		else
			$this->joins[] = new JoinClause(JoinClause::INNER_JOIN, $table, $alias_or_cond, $cond);
		
		return $this;
	}
	
	public function leftJoin($table, $alias_or_cond, $cond = null) {
		if (is_null($cond))
			$this->joins[] = new JoinClause(JoinClause::LEFT_JOIN, $table, null, $alias_or_cond);
		else
			$this->joins[] = new JoinClause(JoinClause::LEFT_JOIN, $table, $alias_or_cond, $cond);
		
		return $this;
	}
	
	public function fullOuterJoin($table, $alias_or_cond, $cond = null) {
		if (is_null($cond))
			$this->joins[] = new JoinClause(JoinClause::FULL_OUTER_JOIN, $table, null, $alias_or_cond);
		else
			$this->joins[] = new JoinClause(JoinClause::FULL_OUTER_JOIN, $table, $alias_or_cond, $cond);
		
		return $this;
	}
	
	
	public function order_by() {
		$this->orderBy = func_get_args();
		return $this;
	}
	
	public function limit($limit) {
		$this->limitClause = $limit;
		return $this;	
	}
	
	public function offset($offset) {
		$this->offsetClause = $offset;
		return $this;
	}
	
	public function group_by() {
		$this->groupBy = func_get_args();
		return $this;
	}
	
	public function having($column) {
		$this->havingClause = $column;
		return $this;
	}
	
	protected function translateColumn($column) {
		if ($column instanceof Column) {
			$path = $column->getPath();
			
			if (empty($path))
				return is_null($this->alias) ? $column->getName() : $this->alias . '.' . $column->getName();
			
			$references = $column->getPath()[0];
				
			if (!array_key_exists($references, $this->tableMapping))
				throw new \UnexpectedValueException("SelectQuery: Column {$column->getName()} references an unknown table '$references'");
				
			if (is_null($this->tableMapping[$references]))
				return $column->getName();
			else
				return $this->tableMapping[$references] . '.' . $column->getName();
		}
		elseif (is_string($column) && !empty($column)) {
			//TODO: strstr($column, '.')
			return $column;
		}
		else
			throw new \InvalidArgumentException("SelectQuery: Columns must be specified either by a Column instance or a non-empty string");
	}
	
	protected function getColumnsExpression() {
		if (empty($this->columns))
			return '*';
		
		$columns = [];
		
		foreach($this->columns as $column)
			$columns[] = $this->translateColumn($column);
		
		return implode(',', $columns);
	}
	
	protected function getFromClause() {
		if (isset($this->alias))
			return $this->table . ' ' . $this->alias;
		
		return $this->table;
	}
	
	protected function getAdditionalClauses() {
		$clauses = '';
		
		if (!empty($this->groupBy)) {
			$group_by = [];
			
			foreach($this->groupBy as $group)
				$group_by[] = $this->translateColumn($group);
			
			$clauses .= implode(',', $group_by) . ' ';
			
			if (isset($this->havingClause)) {
				//TODO:
			}
		}
		
		if (!empty($this->orderBy)) {
			$order_by = [];
			
			foreach($this->orderBy as $order)
				$order_by[] = $this->translateColumn($order);
			
			$clauses .= implode(',', $order_by) . ' ';
		}
		
		if (isset($this->limitClause))
			$clauses .= "LIMIT {$this->limitClause} ";
		
		if (isset($this->offsetClause))
			$clauses .= "OFFSET {$this->offsetClause}";
		
		return $clauses;
	}
	
	protected function buildTableMapping() {
		$this->tableMapping[$this->table] = $this->alias;
	}
	
	public function build() {
		$this->buildTableMapping();
		$columns = $this->getColumnsExpression();
		$from = $this->getFromClause();
		$clauses = $this->getAdditionalClauses();
		
		return rtrim("SELECT $columns FROM $from $clauses");
	}
	
	public function fetch($mapping_type = null) {
		$query = $this->build();		
		$mapper = $this->query->getMapper();
		
		if (is_null($mapping_type))
			return $mapper->merge($this->config)->query($query);
		
		$config = $this->config;
		$config['map.type'] = $mapping_type;
		return $mapper->merge($config)->query($query);
	}
}
?>