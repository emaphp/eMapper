<?php
namespace eMapper\SQL\Fluent\Clause;

use eMapper\Engine\Generic\Driver;
use eMapper\SQL\Predicate\SQLPredicate;
use eMapper\SQL\Field\FluentFieldTranslator;

class FromClause {
	/**
	 * Connection driver
	 * @var Driver
	 */
	protected $driver;
	
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
	 * Table joins
	 * @var array
	 */
	protected $joins = [];
	
	/**
	 * Joined tables list
	 * @var array
	 */
	protected $tableList = [];
	
	/**
	 * Join arguments
	 * @var array
	 */
	protected $args = [];
	
	public function __construct(Driver $driver, $table, $alias) {
		$this->driver = $driver;
		$this->table = $table;
		$this->alias = $alias;
		
		$this->tableList[$table] = $table;
		
		if (isset($alias))
			$this->tableList[$alias] = $table;
	}
	
	/**
	 * Adds an inner join
	 * @param string $table
	 * @param string|SQLPredicate $alias_or_cond
	 * @param string|SQLPredicate $cond
	 */
	public function addInnerJoin($table, $alias_or_cond, $cond) {
		if (is_null($cond)) { //no alias
			$this->joins[$table] = new JoinClause(JoinClause::INNER_JOIN, $table, null, $alias_or_cond);
			$this->tableList[$table] = $table;
		}
		else {
			$this->joins[$table] = new JoinClause(JoinClause::INNER_JOIN, $table, $alias_or_cond, $cond);
			$this->tableList[$alias_or_cond] = $table;
			$this->tableList[$table] = $table;
		}
	}
	
	/**
	 * Adds a left join
	 * @param string $table
	 * @param string|SQLPredicate $alias_or_cond
	 * @param string|SQLPredicate $cond
	 */
	public function addLeftJoin($table, $alias_or_cond, $cond) {
		if (is_null($cond)) {
			$this->joins[$table] = new JoinClause(JoinClause::LEFT_JOIN, $table, null, $alias_or_cond);
			$this->tableList[$table] = $table;
		}
		else {
			$this->joins[$table] = new JoinClause(JoinClause::LEFT_JOIN, $table, $alias_or_cond, $cond);
			$this->tableList[$alias_or_cond] = $table;
			$this->tableList[$table] = $table;
		}
	}
	
	/**
	 * Adds a full outer join
	 * @param string $table
	 * @param string|SQLPredicate $alias_or_cond
	 * @param string|SQLPredicate $cond
	 */
	public function addFullOuterJoin($table, $alias_or_cond, $cond) {
		if (is_null($cond)) {
			$this->joins[$table] = new JoinClause(JoinClause::FULL_OUTER_JOIN, $table, null, $alias_or_cond);
			$this->tableList[$table] = $table;
		}
		else {
			$this->joins[$table] = new JoinClause(JoinClause::FULL_OUTER_JOIN, $table, $alias_or_cond, $cond);
			$this->tableList[$alias_or_cond] = $table;
			$this->tableList[$table] = $table;
		}
	}
	
	/**
	 * Returns a conditional clause as a string
	 * @param string|SQLPredicate $condition
	 * @throws \InvalidArgumentException
	 * @return string
	 */
	protected function translateCondition($condition) {
		if ($condition instanceof SQLPredicate) {
			$translator = new FluentFieldTranslator($this->tableList);
			return $condition->evaluate($translator, $this->driver, $this->args);
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
		$expr = $join->getJoinType() . ' ';
	
		if ($join->getAlias())
			$expr .= $join->getTable() . ' ' . $join->getAlias() . ' ON ' . $this->translateCondition($join->getCondition()) . ' ';
		else
			$expr .= $join->getTable() .  ' ON ' . $this->translateCondition($join->getCondition()) . ' ';
	
		return $expr;
	}
	
	public function build() {
		$from = '';
		
		if (isset($this->alias))
			$from .= $this->table . ' ' . $this->alias . ' ';
		else
			$from .= $this->table . ' ';
		
		foreach ($this->joins as $join)
			$from .= $this->translateJoin($join);
		
		return $from;
	}
	
	public function getTable() {
		return $this->table;
	}
	
	public function getAlias() {
		return $this->alias;
	}
	
	public function getTableList() {
		return $this->tableList;
	}
	
	public function getArguments() {
		return $this->args;
	}
}
?>