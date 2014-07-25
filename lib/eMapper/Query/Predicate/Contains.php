<?php
namespace eMapper\Query\Predicate;

use eMapper\Engine\Generic\Driver;

class Contains extends StringComparisonPredicate {
	public function render() {
		$negate_op = $this->negate ? 'NOT' : '';
		$op = $this->case_sensitive ? 'LIKE' : 'ILIKE';
		return "$negate_op (%s $op [?s (. '%' (addcslashes (%0) '_%') '%') ?])";
	}
	
	protected function formatExpression(Driver $driver, $expression) {
		return '%' . addcslashes($expression, '_%') . '%';
	}
}
?>