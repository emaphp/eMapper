<?php
namespace eMapper\Query\Predicate;

use eMapper\Reflection\Profile\ClassProfile;
use eMapper\Engine\Generic\Driver;

/**
 * The In class defines a predicate for IN clauses.
 * @author emaphp
 */
class In extends ComparisonPredicate {
	public function render(Driver $driver) {
		$op = $this->negate ? 'NOT' : '';
		return "%s $op IN (%s)";	
	}
	
	protected function buildComparisonExpression(Driver $driver) {
		if ($this->negate) {
			return "%s NOT IN (%s)";
		}
		
		return "%s IN (%s)";
	}
}
?>