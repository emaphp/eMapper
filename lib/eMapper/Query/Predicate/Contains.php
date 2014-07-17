<?php
namespace eMapper\Query\Predicate;

use eMapper\Engine\Generic\Driver;

class Contains extends StringComparisonPredicate {
	protected function formatExpression(Driver $driver, $expression) {
		return '%' . $expression . '%';
	}
}
?>