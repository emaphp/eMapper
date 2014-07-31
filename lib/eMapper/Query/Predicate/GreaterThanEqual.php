<?php
namespace eMapper\Query\Predicate;

use eMapper\Engine\Generic\Driver;

/**
 * The GreaterThanEqual class defines a predicate for values greater than or equal than
 * a given expression.
 * @author emaphp
 */
class GreaterThanEqual extends ComparisonPredicate {
	public function render(Driver $driver) {
		$op = $this->negate ? '<' : '>=';
		return "%s $op %s";
	}
	
	protected function buildComparisonExpression(Driver $driver) {
		 if ($this->negate) {
		 	return '%s < %s';
		 }
		 
		 return '%s >= %s';
	}
}
?>