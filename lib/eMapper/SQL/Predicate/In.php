<?php
namespace eMapper\SQL\Predicate;

use eMapper\Engine\Generic\Driver;

/**
 * The In class defines a predicate for IN clauses.
 * @author emaphp
 */
class In extends ComparisonPredicate {
	public function generate(Driver $driver) {
		$op = $this->negate ? 'NOT' : '';
		return "%s $op IN (%s)";	
	}
	
	protected function buildComparisonExpression(Driver $driver) {
		return $this->negate ? "%s NOT IN (%s)" : "%s IN (%s)"; 
	}
}