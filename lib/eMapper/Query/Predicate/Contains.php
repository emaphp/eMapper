<?php
namespace eMapper\Query\Predicate;

use eMapper\Reflection\Profile\ClassProfile;
use eMapper\Engine\Generic\Driver;

class Contains extends SQLPredicate {
	public function evaluate(Driver $driver, ClassProfile $profile, $args, $arg_index = 0) {
	}
}
?>