<?php
namespace eMapper\Engine\MySQL\Regex;

use eMapper\Engine\Generic\Regex\GenericRegex;

class MySQLRegex extends GenericRegex {
	public function filter($expression) {
		return $this->case_sensitive ? $expression : strtolower($expression);
	}
	
	public function comparisonExpression($negate) {
		if ($this->case_sensitive) {
			$op = $negate ? 'NOT REGEXP BINARY' : 'REGEXP BINARY';
			return "%s $op %s";
		}
		
		$op = $negate ? 'NOT REGEXP' : 'REGEXP'';
		return "%s $op %s";
	}
}
?>