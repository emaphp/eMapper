<?php
namespace eMapper\Query\Predicate;

use eMapper\Engine\Generic\Driver;
use eMapper\Engine\Generic\Regex\GenericRegex;

/**
 * The Contains class defines a predicate for strings that include a given value.
 * @author emaphp
 */
class Contains extends StringComparisonPredicate {
	public function render(Driver $driver) {
		$regex = $driver->getRegex();
		$regex->setOptions($this->case_sensitive, $this->negate);
		return $regex->dynamicExpression(GenericRegex::CONTAINS);
	}
	
	protected function formatExpression(Driver $driver, $expression) {
		return '%' . addcslashes($expression, '_%') . '%';
	}
	
	protected function buildComparisonExpression(Driver $driver) {
		$regex = $driver->getRegex();
		$regex->setOptions($this->case_sensitive, $this->negate);
		return $regex->comparisonExpression(GenericRegex::CONTAINS);
	}
}
?>