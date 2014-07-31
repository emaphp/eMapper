<?php
namespace eMapper\Query\Predicate;

use eMapper\Engine\Generic\Driver;

/**
 * The Equal class represents the default comparison predicate.
 * @author emaphp
 */
class Equal extends ComparisonPredicate {
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