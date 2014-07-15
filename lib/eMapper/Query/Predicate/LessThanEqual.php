<?php
namespace eMapper\Query\Predicate;

use eMapper\Engine\Generic\Driver;

class LessThanEqual extends ComparisonPredicate {
	protected function formatExpression(Driver $driver, $expression) {
		return $expression;
	}
	
	protected function buildComparisonExpression(Driver $driver) {
		 if ($this->negate) {
		 	return '%s > %s';
		 }
		 
		 return '%s <= %s';
	}
}
?>