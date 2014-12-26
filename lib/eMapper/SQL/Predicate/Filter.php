<?php
namespace eMapper\SQL\Predicate;

use eMapper\Engine\Generic\Driver;
use eMapper\Query\Cond;
use eMapper\Query\Schema;

/**
 * The Filter class is a container for various types of predicates.
 * @author emaphp
 */
class Filter extends SQLPredicate {
	/**
	 * Predicate list
	 * @var array:\eMapper\SQL\Predicate\SQLPredicate
	 */
	protected $predicates;
	
	/**
	 * Logical operator
	 * @var string
	 */
	protected $operator;
	
	public function __construct(array $predicates, $negate = false, $operator = Cond::LOGICAL_AND) {
		$this->predicates = $predicates;
		$this->negate = $negate;
		$this->operator = $operator;
	}
	
	public function getPredicates() {
		return $this->predicates;
	}
	
	public function getOperator() {
		return $this->operator;
	}
	
	public function evaluate(Driver $driver, Schema &$schema) {
		if (empty($this->predicates))
			return '';
		
		if (count($this->predicates) == 1) {
			if (!empty($this->alias))
				$this->predicates[0]->setAlias($this->alias);
			$condition = $this->predicates[0]->evaluate($driver, $schema);
			return ($this->negate) ? 'NOT ' . $condition : $condition;
		}
		
		$predicates = [];
		foreach ($this->predicates as $predicate) {
			if (!empty($this->alias))
				$predicate->setAlias($this->alias);
			$predicates[] = $predicate->evaluate($driver, $schema);
		}
		
		$condition = '( ' . implode(" {$this->operator} ", $predicates) . ' )';
		return ($this->negate) ? 'NOT ' . $condition : $condition;
	}
		
	public function generate(Driver $driver) {
		if (empty($this->predicates))
			return '';
		
		if (count($this->predicates) == 1) {
			$condition = $this->predicates[0]->render($driver);
			return $this->negate ? 'NOT (' . $condition . ')' : $condition;
		}
		
		$predicates = [];
		foreach ($this->predicates as $predicate)
			$predicates[] = $predicate->generate($driver);
		
		$condition = '( ' . implode(" {$this->operator} ", $predicates) . ' )';
		return $this->negate ? 'NOT ' . $condition : $condition;
	}
}