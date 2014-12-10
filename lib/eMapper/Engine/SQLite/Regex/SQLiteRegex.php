<?php
namespace eMapper\Engine\SQLite\Regex;

use eMapper\Engine\Generic\Regex\GenericRegex;

/**
 * The SQLiteRegex clsas builds string expression for regex predicates.
 * @author emaphp
 */
class SQLiteRegex extends GenericRegex {
	public function getDynamicExpression($type) {
		switch ($type) {
			case self::CONTAINS:
			{
				$op = $this->negate ? 'NOT LIKE' : 'LIKE';
				return "%s $op [?s (. '%%' (addcslashes (%%0) '%%_') '%%') ?]";
			}
					
			case self::STARTS_WITH:
			{
				$op = $this->negate ? 'NOT LIKE' : 'LIKE';
				return "%s $op [?s (. (addcslashes (%%0) '%%_') '%%') ?]";
			}
					
			case self::ENDS_WITH:
			{
				$op = $this->negate ? 'NOT LIKE' : 'LIKE';
				return "%s $op [?s (. '%%' (addcslashes (%%0) '%%_')) ?]";
			}
							
			case self::REGEX:
			{
				$op = $this->negate ? 'NOT REGEXP' : 'REGEXP';
				
				if ($this->caseSensitive)
					return "%s $op [?s (%%0) ?]";
				
				return "%s $op [?s (. '(?i)' (%%0)) ?]";
			}
		}
	}
	
	public function formatString($expression) {
		if ($this->caseSensitive)
			return $expression;
		
		return '(?i)' . $expression;
	}
	
	public function getComparisonExpression($type) {
		switch ($type) {
			case self::CONTAINS:
			case self::STARTS_WITH:
			case self::ENDS_WITH:
			{
				$op = $this->negate ? 'NOT LIKE' : 'LIKE';
				return "%s $op %s";
			}
			
			case self::REGEX:
			{
				$op = $this->negate ? 'NOT REGEXP' : 'REGEXP';
				return "%s $op %s";
			}
		}
	}
}