<?php
namespace eMapper\Query\Predicate;

use eMapper\Reflection\Profile\ClassProfile;
use eMapper\Engine\Generic\Driver;

class Regex extends ComparisonPredicate {
	/**
	 * Indicates if the comparison is case sensitive
	 * @var boolean
	 */
	protected $case_sensitive;
	
	public function __construct($field, $expression, $case_sensitive = true) {
		parent::__construct($field, $expression);
		$this->case_sensitive = $case_sensitive;
	}

	protected function comparisonExpression(Driver $driver, &$args, $index) {
		$regex = $driver->regex();
		$args[$index] = $regex->filter($args[$index], $this->case_sensitive);
		return $regex->comparisonExpression($this->case_sensitive);
	}
}
?>