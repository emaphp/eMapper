<?php
namespace eMapper\Query\Predicate;

use eMapper\Engine\Generic\Driver;

class Regex extends StringComparisonPredicate {
	public function render() {
		$regex = $driver->getRegex();
		$regex->setOptions($this->case_sensitive, $this->negate);

		//get regex operator
		$op = trim(sprintf($regex->comparisonExpression(), '', ''));
		$expr = $regex->argumentExpression();
		return "%s $op $expr";
	}
	
	protected function formatExpression(Driver $driver, $expression) {
		$regex = $driver->getRegex();
		$regex->setOptions($this->case_sensitive, $this->negate);
		return $regex->filter($expression);
	}
	
	protected function buildComparisonExpression(Driver $driver) {
		$regex = $driver->getRegex();
		$regex->setOptions($this->case_sensitive, $this->negate);
		return $regex->comparisonExpression();
	}
}
?>