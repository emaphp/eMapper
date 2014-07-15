<?php
namespace eMapper\Query\Predicate;

use eMapper\Engine\Generic\Driver;

class Regex extends StringComparisonPredicate {
	protected function formatExpression(Driver $driver, $expression) {
		$regex = $driver->regex($this->case_sensitive);
		return $regex->filter($expression);
	}
	
	protected function buildComparisonExpression(Driver $driver) {
		$regex = $driver->regex($this->case_sensitive);
		return $regex->comparisonExpression($this->negate);
	}
}
?>