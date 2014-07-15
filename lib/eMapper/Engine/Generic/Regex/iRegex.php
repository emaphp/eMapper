<?php
namespace eMapper\Engine\Generic\Regex;

interface iRegex {
	public function filter($expression, $case_sensitive);
	public function comparisonExpression($case_sensitive);
}
?>