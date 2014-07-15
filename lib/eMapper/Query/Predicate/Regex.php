<?php
namespace eMapper\Query\Predicate;

use eMapper\Engine\Generic\Driver;

class Regex extends StringComparisonPredicate {
	protected function formatExpression(Driver $driver, $expression) {
		$regex = $driver->regex();
		return $regex->filter($expression, $this->case_sensitive);
	}
	
	protected function buildComparisonExpression(Driver $driver) {
		$regex = $driver->regex();
		return $regex->comparisonExpression($this->case_sensitive);
	}
}
?>