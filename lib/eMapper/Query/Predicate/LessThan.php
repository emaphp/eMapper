<?php
namespace eMapper\Query\Predicate;

use eMapper\Engine\Generic\Driver;

class LessThan extends ComparisonPredicate {
	public function render(Driver $driver) {
		$op = $this->negate ? '>=' : '<';
		return "%s $op %s";
	}
	
	protected function buildComparisonExpression(Driver $driver) {
		 if ($this->negate) {
		 	return '%s >= %s';
		 }
		 
		 return '%s < %s';
	}
}
?>