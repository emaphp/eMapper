<?php
namespace eMapper\SQL\Fluent;

class JoinClause {
	//joi types
	const LEFT_JOIN       = 0;
	const INNER_JOIN      = 1;
	const FULL_OUTER_JOIN = 2;
	
	/**
	 * Join type
	 * @var int
	 */
	protected $type;
	
	/**
	 * Joined table
	 * @var string
	 */
	protected $table;
	
	/**
	 * Joined table alias
	 * @var string
	 */
	protected $alias;
	
	/**
	 * Join condition
	 * @var string|SQLPredicate
	 */
	protected $condition;
	
	public function __construct($type, $table, $alias, $condition) {
		$this->type = $type;
		$this->table = $table;
		$this->alias = $alias;
		$this->condition = $condition;
	}
	
	public function getType() {
		return $this->type;
	}
	
	public function getTable() {
		return $this->table;
	}
	
	public function getAlias() {
		return $this->alias;
	}
	
	public function getCondition() {
		return $this->condition;
	}
	
	/**
	 * Returns a join type as a string
	 * @return string
	 */
	public function getJoinType() {
		switch ($this->type) {
			case self::LEFT_JOIN:
				return 'LEFT JOIN';
				
			case self::INNER_JOIN:
				return 'INNER JOIN';
				
			case FULL_OUTER_JOIN:
				return 'FULL OUTER JOIN';
		}
	}
}
?>