<?php
namespace eMapper\Query\Predicate;

use eMapper\Reflection\Profile\ClassProfile;
use eMapper\Query\Field;
use eMapper\Engine\Generic\Driver;

class Range extends SQLPredicate {
	public function __construct(Field$field, $from, $to) {
		parent::__construct($field, null);
		$this->from = $from;
		$this->to = $to;
	}
	
	public function evaluate(Driver $driver, ClassProfile $profile, $args, $arg_index = 0) {
	}
}
?>