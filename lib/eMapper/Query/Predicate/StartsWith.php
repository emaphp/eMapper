<?php
namespace eMapper\Query\Predicate;

use eMapper\Engine\Generic\Driver;

class StartsWith extends StringComparisonPredicate {
	protected function formatExpression(Driver $driver, $expression) {
		return addcslashes($expression, '%_') . '%';
	}
}
?>