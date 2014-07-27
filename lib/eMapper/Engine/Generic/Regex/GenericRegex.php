<?php
namespace eMapper\Engine\Generic\Regex;

abstract class GenericRegex {
	/**
	 * Indicates if the regular expression is case sensitive
	 * @var boolean
	 */
	protected $case_sensitive;
	
	/**
	 * Indicates if the predicate must be negate
	 * @var boolean
	 */
	protected $negate;
	
	public function setOptions($case_sensitive, $negate) {
		$this->case_sensitive = $case_sensitive;
		$this->negate = $negate;
	}
	
	public function filter($expression) {
		return $expression;
	}
	
	public function argumentExpression() {
		return '[?s (%0) ?]';
	}
	
	public abstract function comparisonExpression();
}
?>