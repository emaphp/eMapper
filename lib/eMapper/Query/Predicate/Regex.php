<?php
namespace eMapper\Query\Predicate;

use eMapper\Engine\Generic\Driver;

class Regex extends StringComparisonPredicate {
	protected function comparisonExpression(Driver $driver, &$args, $index) {
		$regex = $driver->regex();
		$args[$index] = $regex->filter($args[$index], $this->case_sensitive);
		return $regex->comparisonExpression($this->case_sensitive);
	}
}
?>