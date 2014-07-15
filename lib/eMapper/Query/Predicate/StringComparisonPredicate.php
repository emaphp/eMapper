<?php
namespace eMapper\Query\Predicate;

use eMapper\Engine\Generic\Driver;

abstract class StringComparisonPredicate extends ComparisonPredicate {
	/**
	 * Indicates if the comparison is case sensitive
	 * @var boolean
	 */
	protected $case_sensitive;
	
	public function __construct($field, $expression, $case_sensitive, $negate) {
		parent::__construct($field, $expression, $negate);
		$this->case_sensitive = $case_sensitive;
	}
	
	protected function buildComparisonExpression(Driver $driver) {
		if ($this->case_sensitive) {
			$op = $this->negate ? 'NOT LIKE' : 'LIKE';
			return "%s $op %s";
		}
	
		$op = $this->negate ? 'NOT ILIKE' : 'ILIKE';
		return "%s $op %s";
	}
}
?>