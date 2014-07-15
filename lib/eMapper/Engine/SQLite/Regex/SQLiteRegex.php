<?php
namespace eMapper\Engine\SQLite\Regex;

use eMapper\Engine\Generic\Regex\iRegex;

class SQLiteRegex implements iRegex {
	public function filter($expression, $case_sensitive) {
		if ($case_sensitive) {
			return $expression;
		}
		
		return '(?i)' . strtolower($expression);
	}
	
	public function comparisonExpression($case_sensitive) {
		return '%s REGEXP %s';
	}
}
?>