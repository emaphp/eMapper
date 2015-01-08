<?php
namespace eMapper\SQL\Predicate;

use eMapper\Query\Field;

/**
 * The StringComparisonPredicate class adds the case-sensitive attribute for string values comparison.
 * @author emaphp
 */
abstract class StringComparisonPredicate extends ComparisonPredicate {
	/**
	 * Indicates if the comparison is case sensitive
	 * @var boolean
	 */
	protected $caseSensitive;
	
	public function __construct(Field $field, $caseSensitive, $negate, $expression = null) {
		parent::__construct($field, $negate, $expression);
		$this->caseSensitive = $caseSensitive;
	}
	
	public function getCaseSensitive() {
		return $this->caseSensitive;
	}
}