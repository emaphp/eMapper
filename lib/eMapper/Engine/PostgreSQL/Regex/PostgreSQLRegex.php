<?php
namespace eMapper\Engine\PostgreSQL\Regex;

use eMapper\Engine\Generic\Regex\GenericRegex;

class PostgreSQLRegex extends GenericRegex {
	public function dynamicExpression($type) {
		switch ($type) {
			case self::CONTAINS:
			{
				$op = $this->negate ? 'NOT LIKE' : 'LIKE';
					
				if ($this->case_sensitive) {
					return "%s $op [?s (. '%%' (addcslashes (%%0) '%%_') '%%') ?]";
				}
					
				return "LOWER(%s) $op LOWER([?s (. '%%' (addcslashes (%%0) '%%_') '%%') ?])";
			}
			break;
					
			case self::STARTS_WITH:
			{
				$op = $this->negate ? 'NOT LIKE' : 'LIKE';
						
				if ($this->case_sensitive) {
					return "%s $op [?s (. (addcslashes (%%0) '%%_') '%%') ?]";
				}
				
				return "LOWER(%s) $op LOWER([?s (. (addcslashes (%%0) '%%_') '%%') ?])";
			}
			break;
					
			case self::ENDS_WITH:
			{
				$op = $this->negate ? 'NOT LIKE' : 'LIKE';
		
				if ($this->case_sensitive) {
					return "%s $op [?s (. '%%' (addcslashes (%%0) '%%_')) ?]";
				}
				
				return "LOWER(%s) $op LOWER([?s (. '%%' (addcslashes (%%0) '%%_')) ?])";
			}
			break;
							
			case self::REGEX:
			{					
				if ($this->case_sensitive) {
					$op = $this->negate ? '!~' : '~';
					return "%s $op [?s (%%0) ?]";
				}
					
				$op = $this->negate ? '!~*' : '~*';
				return "%s $op [?s (%%0) ?]";
			}
			break;
		}
	}
		
	public function comparisonExpression($type) {
		switch ($type) {
			case self::CONTAINS:
			case self::STARTS_WITH:
			case self::ENDS_WITH:
			{
				$op = $this->negate ? 'NOT LIKE' : 'LIKE';
					
				if ($this->case_sensitive) {
					return "%s $op %s";
				}
					
				return "LOWER(%s) $op LOWER(%s)";
			}
			break;
					
			case self::REGEX:
			{
				if ($this->case_sensitive) {
					$op = $this->negate ? '!~' : '~';
					return "%s $op %s";
				}
				
				$op = $this->negate ? '!~*' : '~*';
				return "%s $op %s";
			}
			break;
		}
	}
}
?>