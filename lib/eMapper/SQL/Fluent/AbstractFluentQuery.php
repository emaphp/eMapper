<?php
namespace eMapper\SQL\Fluent;

use eMapper\Query\FluentQuery;
use eMapper\Statement\Configuration\StatementConfiguration;
use eMapper\SQL\Fluent\Clause\WhereClause;
use eMapper\SQL\Fluent\Clause\FromClause;

/**
 * The AbstractFluentQuery class encapsulates the logic shared between fluent queries
 * @author emaphp
 */
abstract class AbstractFluentQuery {
	use StatementConfiguration;
	
	/**
	 * The parent query this instance is created from
	 * @var FluentQuery
	 */
	protected $fluent;
	
	/**
	 * FROM clause
	 * @var FromClause
	 */
	protected $fromClause;
	
	/**
	 * WHERE clause
	 * @var WhereClause
	 */
	protected $whereClause;
	
	/**
	 * Field translator
	 * @var FieldTranslator
	 */
	protected $translator;
	
	public function __construct(FluentQuery $fluent, $table, $alias = null) {
		$this->fluent = $fluent;
		$this->fromClause = new FromClause($fluent->getMapper()->getDriver(), $table, $alias);
		$this->preserveInstance = true;
	}
	
	/*
	 * JOINS
	 */
	
	/**
	 * Adds an inner join to the current query
	 * @param string $table
	 * @param string|SQLPredicate $alias_or_cond
	 * @param string|SQLPredicate $cond
	 * @return \eMapper\SQL\Fluent\AbstractFluentQuery
	 */
	public function innerJoin($table, $alias_or_cond, $cond = null) {
		$this->fromClause->addInnerJoin($table, $alias_or_cond, $cond);
		return $this;
	}
	
	/**
	 * Adds a left join to the current query
	 * @param string $table
	 * @param string|SQLPredicate $alias_or_cond
	 * @param string|SQLPredicate $cond
	 * @return \eMapper\SQL\Fluent\AbstractFluentQuery
	 */
	public function leftJoin($table, $alias_or_cond, $cond = null) {
		$this->fromClause->addLeftJoin($table, $alias_or_cond, $cond);
		return $this;
	}
	
	/**
	 * Adds a full outer join to the current query
	 * @param string $table
	 * @param string|SQLPredicate $alias_or_cond
	 * @param string|SQLPredicate $cond
	 * @return \eMapper\SQL\Fluent\AbstractFluentQuery
	 */
	public function fullOuterJoin($table, $alias_or_cond, $cond = null) {
		$this->fromClause->addFullOuterJoin($table, $alias_or_cond, $cond);
		return $this;
	}
	
	/**
	 * Returns the query as a sql string and its arguments
	 * @return array
	 */
	public abstract function build();
	
	/*
	 * FROM
	 */
	
	/**
	 * Returns a FROM clause for the current query
	 * @return string
	 */
	protected function buildFromClause() {
		return $this->fromClause->build();
	}
	
	/*
	 * WHERE
	 */
	
	/**
	 * Returns a WHERE clause as a string
	 * @return string
	 */
	protected function buildWhereClause() {
		if (isset($this->whereClause)) {
			return $this->whereClause->build($this->translator, $this->fluent->getMapper()->getDriver());
		}

		return '';
	}
	
	/**
	 * Sets a WHERE clause for the current query
	 * @param SQLPredicate|string $where
	 * @return \eMapper\SQL\Fluent\AbstractFluentQuery
	 */
	public function where($where) {
		$this->whereClause = new WhereClause(func_get_args());
		return $this;
	}
	
	/**
	 * Executes the query
	 * @return boolean
	 */
	public function exec() {
		list($query, $args) = $this->build();
		array_unshift($args, $query);
		if (empty($this->config))
			return call_user_func_array([$this->fluent->getMapper(), 'sql'], $args);
		return call_user_func_array([$this->fluent->getMapper()->merge($this->config), 'sql'], $args);
	}
}
?>