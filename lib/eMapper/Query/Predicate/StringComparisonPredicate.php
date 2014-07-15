<?php
namespace eMapper\Query\Predicate;

use eMapper\Engine\Generic\Driver;

abstract class StringComparisonPredicate extends ComparisonPredicate {
	/**
	 * Indicates if the comparison is case sensitive
	 * @var boolean
	 */
	protected $case_sensitive;
	
	public function __construct($field, $expression, $case_sensitive = true) {
		parent::__construct($field, $expression);
		$this->case_sensitive = $case_sensitive;
	}
}
?>