<?php
namespace eMapper\Query\Predicate;

use eMapper\Engine\Generic\Driver;

class EndsWith extends StringComparisonPredicate {
	protected function comparisonExpression(Driver $driver, &$args, $index) {
		$args[$index] = '%' . $args[$index];
		
		if ($this->case_sensitive) {
			return '%s LIKE %s';
		}
		
		return '%s ILIKE %s';
	}
}
?>