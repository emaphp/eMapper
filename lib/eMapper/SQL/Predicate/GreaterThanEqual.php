<?php
namespace eMapper\SQL\Predicate;

use eMapper\Engine\Generic\Driver;

/**
 * The GreaterThanEqual class defines a predicate for values greater than or equal than a given expression.
 * @author emaphp
 */
class GreaterThanEqual extends ComparisonPredicate {
	public function generate(Driver $driver) {
		$op = $this->negate ? '<' : '>=';
		return "%s $op %s";
	}
	
	protected function buildComparisonExpression(Driver $driver) {
		 return $this->negate ? '%s < %s' : '%s >= %s';  
	}
}