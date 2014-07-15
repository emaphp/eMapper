<?php
namespace eMapper\Query\Predicate;

use eMapper\Reflection\Profile\ClassProfile;
use eMapper\Engine\Generic\Driver;

class In extends ComparisonPredicate {
	protected function formatExpression(Driver $driver, $expression) {
		return $expression;
	}
	
	protected function buildComparisonExpression(Driver $driver) {
		if ($this->negate) {
			return "%s NOT IN (%s)";
		}
		
		return "%s IN (%s)";
	}
}
?>