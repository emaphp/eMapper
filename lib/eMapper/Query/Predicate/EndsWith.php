<?php
namespace eMapper\Query\Predicate;

use eMapper\Engine\Generic\Driver;

class EndsWith extends StringComparisonPredicate {
	protected function formatExpression(Driver $driver, $expression) {
		return '%' . $expression;
	}
	
	protected function buildComparisonExpression(Driver $driver) {
		if ($this->case_sensitive) {
			return '%s LIKE %s';
		}
		
		return '%s ILIKE %s';
	}
}
?>