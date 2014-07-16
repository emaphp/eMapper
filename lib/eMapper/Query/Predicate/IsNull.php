<?php
namespace eMapper\Query\Predicate;

use eMapper\Engine\Generic\Driver;
use eMapper\Reflection\Profile\ClassProfile;
use eMapper\Query\Field;

class IsNull extends SQLPredicate {	
	public function evaluate(Driver $driver, ClassProfile $profile, &$args, $arg_index = 0) {
		$column = $this->field->getColumnName($profile);
		
		if ($this->negate) {
			return "$column IS NOT NULL";
		}
		
		return "$column IS NULL";
	}
}
?>