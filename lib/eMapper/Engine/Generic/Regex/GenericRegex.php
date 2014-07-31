<?php
namespace eMapper\Engine\Generic\Regex;

/**
 * The GenericRegex class is a base class tha encapsulates the common logic between
 * the regex generation classes.
 * @author emaphp
 */
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
	
	/**
	 * Sets the current regex options
	 * @param boolea $case_sensitive
	 * @param boolean $negate
	 */
	public function setOptions($case_sensitive, $negate) {
		$this->case_sensitive = $case_sensitive;
		$this->negate = $negate;
	}
	
	/**
	 * Returns a formatted string to use with the regex operator
	 * @param string $expression
	 * @return string
	 */
	public function filter($expression) {
		return $expression;
	}
	
	/**
	 * Returns a string containing a dynamic sql expression with the logic for the given regex type
	 * @param int $type Regex type
	 * @return string
	 */
	public abstract function dynamicExpression($type);
	
	/**
	 * Returns a string containing the comparison expression for the given regex type
	 * @param int $type
	 * @return string
	 */
	public abstract function comparisonExpression($type);
}
?>