<?php
namespace eMapper\Fluent\Query\Clause;

use eMapper\Fluent\Query\AbstractQuery;
use eMapper\Engine\Generic\Driver;
use eMapper\SQL\Predicate\SQLPredicate;
use eMapper\Query\Schema;

/**
 * The FromClause class is an abstraction of the sql FROM clause.
 * @author emaphp
 */
class FromClause {
	/**
	 * Connection driver
	 * @var \eMapper\Engine\Generic\Driver
	 */
	protected $driver;
	
	/**
	 * Join arguments
	 * @var array
	 */
	protected $args = [];
	
	public function __construct(Driver $driver) {
		$this->driver = $driver;
	}
	
	/**
	 * Returns a join expression as a string
	 * @param \eMapper\Fluent\Query\Clause\JoinClause $join
	 * @return string
	 */
	protected function translateJoin(JoinClause $join, Schema &$schema) {
		$expr = $join->getJoinType() . ' ';
		
		//build condition expression
		$condition = $join->getCondition();
		if ($condition instanceof SQLPredicate)
			$condition = $condition->evaluate($this->driver, $schema);
		elseif (!is_string($condition))
			throw new \InvalidArgumentException("Conditions must be specified either by a SQLPredicate instance or a non-empty string");

		//build join expression
		if ($join->getAlias())
			$expr .= $join->getTable() . ' ' . $join->getAlias() . ' ON ' . $condition . ' ';
		else
			$expr .= $join->getTable() .  ' ON ' . $condition . ' ';
	
		return $expr;
	}
	
	/**
	 * Returns a FROM clause as a string
	 * @param \eMapper\Fluent\Query\AbstractQuery $query
	 * @param \eMapper\Query\Schema $schema
	 * @return string
	 */
	public function build(AbstractQuery $query, Schema &$schema) {
		$from = '';
		
		//obtain table/alias from query
		$table = $query->getTable();
		$alias = $query->getAlias();
		
		if (isset($alias))
			$from .= $table . ' ' . $alias . ' ';
		else
			$from .= $table . ' ';
		
		//add joins
		$joins = $query->getJoins();
		foreach ($joins as $join)
			$from .= $this->translateJoin($join, $schema);
		
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