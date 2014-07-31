<?php
namespace eMapper\Query\Predicate;

use eMapper\Engine\Generic\Driver;

/**
 * The StringComparisonPredicate class adds the case-sensitive attribute for string values comparison.
 * @author emaphp
 */
abstract class StringComparisonPredicate extends ComparisonPredicate {
	/**
	 * Indicates if the comparison is case sensitive
	 * @var boolean
	 */
	protected $case_sensitive;
	
	public function __construct($field, $case_sensitive, $negate) {
		parent::__construct($field, $negate);
		$this->case_sensitive = $case_sensitive;
	}
	
	public function getCaseSensitive() {
		return $this->case_sensitive;
	}
}
?>