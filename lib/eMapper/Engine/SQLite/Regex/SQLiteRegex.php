<?php
namespace eMapper\Engine\SQLite\Regex;

use eMapper\Engine\Generic\Regex\GenericRegex;

class SQLiteRegex extends GenericRegex {
	public function filter($expression) {
		if ($this->case_sensitive) {
			return $expression;
		}
		
		return '(?i)' . strtolower($expression);
	}
	
	public function comparisonExpression($negate) {
		if ($negate) {
			return '%s NOT REGEXP %s';
		}
		
		return '%s REGEXP %s';
	}
	
	public function argumentExpression() {
		return "[?s (. '(?i)' (%0)) ?]";
	}
}
?>