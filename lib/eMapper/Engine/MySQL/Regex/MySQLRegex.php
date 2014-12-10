<?php
namespace eMapper\Engine\MySQL\Regex;

use eMapper\Engine\Generic\Regex\GenericRegex;

/**
 * The MySQLRegex class builds string expressions for regex predicates.
 * @author emaphp
 */
class MySQLRegex extends GenericRegex {
	public function getDynamicExpression($type) {
		switch ($type) {
			case self::CONTAINS:
			{
				$op = $this->negate ? 'NOT LIKE' : 'LIKE';
				
				if ($this->caseSensitive)
					return "%s $op [?s (. '%%' (call 'addcslashes' (%%0) '%%_') '%%') ?]";
				
				return "LOWER(%s) $op LOWER([?s (. '%%' (call 'addcslashes' (%%0) '%%_') '%%') ?])";
			}
			
			case self::STARTS_WITH:
			{
				$op = $this->negate ? 'NOT LIKE' : 'LIKE';
				
				if ($this->caseSensitive)
					return "%s $op [?s (. (call 'addcslashes' (%%0) '%%_') '%%') ?]";
				
				return "LOWER(%s) $op LOWER([?s (. (call 'addcslashes' (%%0) '%%_') '%%') ?])";
			}
			
			case self::ENDS_WITH:
			{
				$op = $this->negate ? 'NOT LIKE' : 'LIKE';
				
				if ($this->caseSensitive)
					return "%s $op [?s (. '%%' (call 'addcslashes' (%%0) '%%_')) ?]";
				
				return "LOWER(%s) $op LOWER([?s (. '%%' (call 'addcslashes' (%%0) '%%_')) ?])";
			}
			
			case self::REGEX:
			{
				$op = $this->negate ? 'NOT REGEXP' : 'REGEXP';
				
				if ($this->caseSensitive)
					return "%s $op BINARY [?s (%%0) ?]";
				
				return "%s $op [?s (%%0) ?]";
			}
		}
	}
	
	public function getComparisonExpression($type) {
		switch ($type) {
			case self::CONTAINS:
			case self::STARTS_WITH:
			case self::ENDS_WITH:
			{
				$op = $this->negate ? 'NOT LIKE' : 'LIKE';
				
				if ($this->caseSensitive)
					return "%s $op %s";
				
				return "LOWER(%s) $op LOWER(%s)";
			}
			
			case self::REGEX:
			{
				if ($this->caseSensitive) {
					$op = $this->negate ? 'NOT REGEXP BINARY' : 'REGEXP BINARY';
					return "%s $op %s";
				}
				
				$op = $this->negate ? 'NOT REGEXP' : 'REGEXP';
				return "%s $op %s";
			}
		}
	}
}