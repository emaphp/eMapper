<?php
namespace eMapper\SQL\Predicate;

use eMapper\Engine\Generic\Driver;

/**
 * The Equal class represents the default comparison predicate.
 * @author emaphp
 */
class Equal extends ComparisonPredicate {
	protected function buildComparisonExpression(Driver $driver) {
		if (is_null($this->expression))
			return $this->negate ? '%s IS NOT %s' : '%s IS %s';
		
		return $this->negate ? '%s <> %s' : '%s = %s';
	}
}