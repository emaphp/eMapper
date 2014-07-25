<?php
namespace eMapper\Query\Predicate;

use eMapper\Engine\Generic\Driver;

class LessThanEqual extends ComparisonPredicate {
	public function render() {
		$op = $this->negate ? '>' : '<=';
		return "%s $op %s";
	}
	
	protected function buildComparisonExpression(Driver $driver) {
		 if ($this->negate) {
		 	return '%s > %s';
		 }
		 
		 return '%s <= %s';
	}
}
?>