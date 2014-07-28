<?php
namespace eMapper\Engine\Generic\Regex;

abstract class GenericRegex {
	/*
	 * Regex type constants
	 */
	const CONTAINS = 0;
	const STARTS_WITH = 1;
	const ENDS_WITH = 2;
	const REGEX = 3;
	
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
	
	public abstract function dynamicExpression($type);
	public abstract function comparisonExpression($type);
}
?>