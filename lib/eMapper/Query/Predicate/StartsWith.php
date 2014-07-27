<?php
namespace eMapper\Query\Predicate;

use eMapper\Engine\Generic\Driver;

class StartsWith extends StringComparisonPredicate {
	public function render() {
		$op = $this->case_sensitive ? 'LIKE' : 'ILIKE';
		$not_op = $this->negate ? 'NOT' : '';
		return "%s $not_op $op [?s (. (addslashes (%%0) '%%_') '%%') ?]";
	}
	
	protected function formatExpression(Driver $driver, $expression) {
		return addcslashes($expression, '%_') . '%';
	}
}
?>