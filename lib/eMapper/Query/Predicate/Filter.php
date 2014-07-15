<?php
namespace eMapper\Query\Predicate;

use eMapper\Engine\Generic\Driver;
use eMapper\Reflection\Profile\ClassProfile;

class Filter extends SQLPredicate {
	protected $predicates;
	
	public function __construct($predicates, $negate = false) {
		$this->predicates = $predicates;
		$this->negate = $negate;
	}
	
	public function evaluate(Driver $driver, ClassProfile $profile, &$args, $arg_index = 0) {
		if (empty($this->predicates)) {
			return '';
		}
		
		if (count($this->predicates) == 1) {
			$condition = $this->predicates->evaluate($driver, $profile, $args, $arg_index);
			
			if ($this->negate) {
				return 'NOT ' . $condition;
			}
			
			return $condition;
		}
		
		$predicates = [];
		
		foreach ($this->predicates as $predicate) {
			$predicates[] = $predicate->evaluate($driver, $profile, $args, $arg_index);
		}
		
		$condition = '( ' . implode(' AND ', $predicates) . ' )';
		
		if ($this->negate) {
			return 'NOT ' . $condition;
		}
		
		return $condition;
	}
}
?>