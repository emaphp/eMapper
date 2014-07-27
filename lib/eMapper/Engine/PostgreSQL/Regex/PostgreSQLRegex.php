<?php
namespace eMapper\Engine\PostgreSQL\Regex;

use eMapper\Engine\Generic\Regex\GenericRegex;

class PostgreSQLRegex extends GenericRegex {
	public function comparisonExpression() {
		if ($this->case_sensitive) {
			$op = $this->negate ? '!~' : '~';
			return "%s $op %s";
		}
		
		$op = $this->negate ? '!~*' : '~*';
		return "%s $op %s";
	}
}
?>