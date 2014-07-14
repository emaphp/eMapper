<?php
namespace eMapper\Query\Predicate;

class Filter extends SQLPredicate {
	protected $predicates;
	
	public function __construct($predicates, $negate = false) {
		$this->predicates = $predicates;
		$this->negate = $negate;
	}
	
	public function evaluate(ClassProfile $profile, &$args, $arg_index = 0) {
	}
}
?>