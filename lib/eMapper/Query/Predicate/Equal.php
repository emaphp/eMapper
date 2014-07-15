<?php
namespace eMapper\Query\Predicate;

use eMapper\Query\Field;
use eMapper\Reflection\Profile\ClassProfile;
use eMapper\Engine\Generic\Driver;

class Equal extends ComparisonPredicate {
	protected function comparisonExpression(Driver $driver, &$args, $index) {
		return '%s = %s';
	}
}
?>