<?php
namespace eMapper\Query\Predicate;

use eMapper\Engine\Generic\Driver;
use eMapper\Reflection\Profile\ClassProfile;
use eMapper\Query\Field;

/**
 * The IsNull class defines a predicate for NULL comparison values.
 * @author emaphp
 */
class IsNull extends SQLPredicate {
	public function render(Driver $driver) {
		if ($this->negate) return '%s IS NOT NULL';
		return '%s IS NULL';
	}
	
	public function evaluate(Driver $driver, ClassProfile $profile, &$joins, &$args, $arg_index = 0) {
		$column = $this->getColumnName($this->field, $profile, $joins);
		
		if ($this->negate) return "$column IS NOT NULL";
		return "$column IS NULL";
	}
}
?>