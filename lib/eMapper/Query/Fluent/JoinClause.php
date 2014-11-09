<?php
namespace eMapper\Query\Fluent;

class JoinClause {
	const LEFT_JOIN       = 0;
	const INNER_JOIN      = 1;
	const FULL_OUTER_JOIN = 2;
	
	protected $type;
	protected $table;
	protected $alias;
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
	
	public function getTypeExpression() {
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