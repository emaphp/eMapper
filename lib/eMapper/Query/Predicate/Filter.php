<?php
namespace eMapper\Query\Predicate;

use eMapper\Engine\Generic\Driver;
use eMapper\Reflection\Profile\ClassProfile;
use eMapper\Query\Q;

class Filter extends SQLPredicate {
	/**
	 * Predicate list
	 * @var array
	 */
	protected $predicates;
	
	/**
	 * Operator
	 * @var string
	 */
	protected $operator;
	
	public function __construct($predicates, $negate = false, $operator = Q::LOGICAL_AND) {
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
	
	public function evaluate(Driver $driver, ClassProfile $profile, &$args, $arg_index = 0) {
		if (empty($this->predicates)) {
			return '';
		}
		
		if (count($this->predicates) == 1) {
			$condition = $this->predicates[0]->evaluate($driver, $profile, $args, $arg_index);
			
			if ($this->negate) {
				return 'NOT ' . $condition;
			}
			
			return $condition;
		}
		
		$predicates = [];
		
		foreach ($this->predicates as $predicate) {
			$predicates[] = $predicate->evaluate($driver, $profile, $args, $arg_index);
		}
		
		$condition = '( ' . implode(" {$this->operator} ", $predicates) . ' )';
		
		if ($this->negate) {
			return 'NOT ' . $condition;
		}
		
		return $condition;
	}
}
?>