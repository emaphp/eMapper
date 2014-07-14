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
	}
}
?>