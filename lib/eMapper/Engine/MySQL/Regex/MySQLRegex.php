<?php
namespace eMapper\Engine\MySQL\Regex;

use eMapper\Engine\Generic\Regex\GenericRegex;

class MySQLRegex extends GenericRegex {
	public function comparisonExpression() {
		if ($this->case_sensitive) {
			$op = $this->negate ? 'NOT REGEXP BINARY' : 'REGEXP BINARY';
			return "%s $op %s";
		}
		
		$op = $this->negate ? 'NOT REGEXP' : 'REGEXP';
		return "%s $op %s";
	}
}
?>