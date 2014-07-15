<?php
namespace eMapper\Query\Predicate;

use eMapper\Query\Field;
use eMapper\Reflection\Profile\ClassProfile;
use eMapper\Engine\Generic\Driver;

class Equal extends ComparisonPredicate {
	protected function formatExpression(Driver $driver, $expression) {
		return $expression;		
	}
	
	protected function buildComparisonExpression(Driver $driver) {
		return '%s = %s';
	}
}
?>