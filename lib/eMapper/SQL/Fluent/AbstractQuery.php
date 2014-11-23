<?php
namespace eMapper\SQL\Fluent;

use eMapper\Query\FluentQuery;
use eMapper\Statement\Configuration\StatementConfiguration;
use eMapper\SQL\Fluent\Clause\WhereClause;
use eMapper\SQL\Field\FluentFieldTranslator;
use eMapper\SQL\Fluent\Clause\FromClause;
use eMapper\SQL\Field\FieldTranslator;

abstract class AbstractQuery {
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
	 * @return \eMapper\SQL\Fluent\AbstractQuery
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
	 * @return \eMapper\SQL\Fluent\AbstractQuery
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
	 * @return \eMapper\SQL\Fluent\AbstractQuery
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
	
	protected function buildWhereClause() {
		if (isset($this->whereClause)) {
			return $this->whereClause->build($this->translator, $this->fluent->getMapper()->getDriver());
		}

		return '';
	}
	
	public function where($where) {
		$this->whereClause = new WhereClause(func_get_args());
		return $this;
	}
	
	/**
	 * Executes the query
	 */
	public function exec() {
		list($query, $args) = $this->build();
		return call_user_func_array([$this->fluent->getMapper(), 'sql'], $args);
	}
}
?>