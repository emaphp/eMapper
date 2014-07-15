<?php
namespace eMapper\Engine\MySQL\Regex;

use eMapper\Engine\Generic\Regex\iRegex;

class MySQLRegex implements iRegex {
	public function filter($expression, $case_sensitive) {
		return $case_sensitive ? $expression : strtolower($expression);
	}
	
	public function comparisonExpression($case_sensitive) {
		if ($case_sensitive) {
			return "%s REGEXP BINARY %s";
		}
		
		return "%s REGEXP %s";
	}
}
?>