<?php
namespace eMapper\Engine\PostgreSQL\Regex;

use eMapper\Engine\Generic\Regex\GenericRegex;

class PostgreSQLRegex extends GenericRegex {
	public function filter($expression) {
		return $this->case_sensitive ? $expression : strtolower($expression);
	}
	
	public function comparisonExpression($negate) {
		if ($this->case_sensitive) {
			$op = $negate ? '!~' : '~';
			return "%s $op %s";
		}
		
		$op = $negate ? '!~*' : '~*';
		return "%s $op %s";
	}
}
?>