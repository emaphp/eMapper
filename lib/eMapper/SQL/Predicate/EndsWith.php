<?php
namespace eMapper\SQL\Predicate;

use eMapper\Engine\Generic\Driver;
use eMapper\Engine\Generic\Regex\GenericRegex;

/**
 * The EndsWith class defines a predicate for strings ending with a given value.
 * @author emaphp
 */
class EndsWith extends StringComparisonPredicate {
	public function render(Driver $driver) {
		$regex = $driver->getRegex();
		$regex->setOptions($this->case_sensitive, $this->negate);
		return $regex->dynamicExpression(GenericRegex::ENDS_WITH);
	}
	
	protected function formatExpression(Driver $driver, $expression) {
		return '%' . addcslashes($expression, '%_');
	}
	
	protected function buildComparisonExpression(Driver $driver) {
		$regex = $driver->getRegex();
		$regex->setOptions($this->case_sensitive, $this->negate);
		return $regex->comparisonExpression(GenericRegex::ENDS_WITH);
	}
}
?>