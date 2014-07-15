<?php
namespace eMapper\Query\Predicate;

use eMapper\Engine\Generic\Driver;

class Equal extends ComparisonPredicate {
	protected function formatExpression(Driver $driver, $expression) {
		return $expression;		
	}
	
	protected function buildComparisonExpression(Driver $driver) {
		if (is_null($this->expression)) {
			if ($this->negate) {
				return '%s IS NOT %s';
			}
			
			return '%s IS %s';
		}
		
		if ($this->negate) {
			return '%s <> %s';
		}
		
		return '%s = %s';
	}
}
?>