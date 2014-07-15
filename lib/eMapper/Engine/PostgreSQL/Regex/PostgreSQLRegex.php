<?php
namespace eMapper\Engine\PostgreSQL\Regex;

use eMapper\Engine\Generic\Regex\iRegex;

class PostgreSQLRegex implements iRegex {
	public function filter($expression, $case_sensitive) {
		return $case_sensitive ? $expression : strtolower($expression);
	}
	
	public function comparisonExpression($case_sensitive) {
		if ($case_sensitive) {
			return '%s ~ %s';
		}
		
		return '%s ~* %s';
	}
}
?>