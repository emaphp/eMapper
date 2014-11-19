<?php
namespace eMapper\SQL\Predicate;

use eMapper\Engine\Generic\Driver;
use eMapper\Engine\Generic\Regex\GenericRegex;

/**
 * The StartsWith class defines a predicate for strings starting with a given value.
 * @author emaphp
 */
class StartsWith extends StringComparisonPredicate {
	public function render(Driver $driver) {
		$regex = $driver->getRegex();
		$regex->setOptions($this->case_sensitive, $this->negate);
		return $regex->dynamicExpression(GenericRegex::STARTS_WITH);
	}
	
	protected function formatExpression(Driver $driver, $expression) {
		return addcslashes($expression, '%_') . '%';
	}
	
	protected function buildComparisonExpression(Driver $driver) {
		$regex = $driver->getRegex();
		$regex->setOptions($this->case_sensitive, $this->negate);
		return $regex->comparisonExpression(GenericRegex::STARTS_WITH);
	}
}
?>