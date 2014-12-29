<?php
namespace eMapper\Fluent\Query;

use eMapper\Statement\Configuration\StatementConfiguration;
use eMapper\Fluent\FluentQuery;
use eMapper\Fluent\Query\Clause\FromClause;
use eMapper\Fluent\Query\Clause\WhereClause;
use eMapper\Fluent\Query\Clause\JoinClause;
use eMapper\Query\Schema;

/**
 * The AbstractQuery class defines the generic behaviour of a fluent query.
 * @author emaphp
 */
abstract class AbstractQuery {
	use StatementConfiguration;
	
	/**
	 * The parent query this instance is created from
	 * @var \eMapper\Fluent\FluentQuery
	 */
	protected $fluent;
	
	/**
	 * Connection driver
	 * @var \eMapper\Engine\Generic\Driver
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
	 * Join schema (table => JoinClause)
	 * @var array[string]:\eMapper\Fluent\Query\Clause\JoinClause
	 */
	protected $joins = [];
	
	/**
	 * FROM clause
	 * @var \eMapper\Fluent\Query\Clause\FromClause
	 */
	protected $fromClause;
	
	/**
	 * WHERE clause
	 * @var \eMapper\Fluent\Query\Clause\WhereClause
	 */
	protected $whereClause;
	
	public function __construct(FluentQuery $fluent, $table, $alias = null) {
		$this->fluent = $fluent;
		$this->driver = $fluent->getMapper()->getDriver();
		$this->fromClause = new FromClause($this->driver);
		$this->table = $table;
		$this->alias = $alias;		
		$this->preserveInstance = true;
	}
	
	/*
	 * GETTERS
	 */
	
	public function getFluent() {
		return $this->fluent;
	}
	
	public function getDriver() {
		return $this->driver;
	}
	
	public function getTable() {
		return $this->table;
	}
	
	public function getAlias() {
		return $this->alias;
	}	
	
	public function getJoins() {
		return $this->joins;
	}
	
	/*
	 * WHERE
	 */
		
	/**
	 * Sets a WHERE clause for the current query
	 * @param SQLPredicate|string $where
	 * @return \eMapper\SQL\Fluent\AbstractFluentQuery
	 */
	public function where($where) {
		$this->whereClause = new WhereClause($this->driver, func_get_args());
		return $this;
	}
	
	/*
	 * JOINS
	 */
	
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
	
	/**
	 * Updates query schema
	 * @param \eMapper\Query\Schema $schema
	 */
	protected function updateSchema(Schema $schema) {
		foreach($schema->getJoins() as $join) {
			$assoc = $join->getAssociation();
			$parent = $join->getParent();
			if (is_null($parent))
				$assoc->appendJoin($this, $this->alias, $join->getAlias(), true);
			else {
				$alias = $schema->getJoin($parent)->getAlias();
				$assoc->appendJoin($this, $alias, $join->getAlias(), true);
			}
		}
	}
	
	/*
	 * EXEC
	 */
	
	public function exec() {
		list($query, $args) = $this->build();
		if (empty($this->config))
			return call_user_func([$this->fluent->getMapper(), 'sql'], $query, $args);
		return call_user_func([$this->fluent->getMapper()->merge($this->config), 'sql'], $query, $args);
	}
	
	/*
	 * ABSTRACT METHODS
	 */
	
	public abstract function build();
}