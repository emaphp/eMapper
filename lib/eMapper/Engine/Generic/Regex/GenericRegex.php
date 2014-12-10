<?php
namespace eMapper\Engine\Generic\Regex;

/**
 * The GenericRegex class is a base class tha encapsulates the common logic for regex generation classes.
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
	 * @var bool
	 */
	protected $caseSensitive;
	
	/**
	 * Indicates if the predicate must be negate
	 * @var bool
	 */
	protected $negate;
	
	/**
	 * Sets the current regex options
	 * @param bool $caseSensitive
	 * @param bool $negate
	 */
	public function setOptions($caseSensitive, $negate) {
		$this->caseSensitive = $caseSensitive;
		$this->negate = $negate;
	}
	
	/**
	 * Returns a formatted string to use with the regex operator
	 * Note: the default behaviour is only overriden by the SQLiteRegex
	 * @param string $expression
	 * @return string
	 */
	public function formatString($expression) {
		return $expression;
	}
	
	/**
	 * Returns a string containing a dynamic sql expression with the logic for the given regex type
	 * @param int $type Regex type
	 * @return string
	 */
	public abstract function getDynamicExpression($type);
	
	/**
	 * Returns a string containing the comparison expression for the given regex type
	 * @param int $type
	 * @return string
	 */
	public abstract function getComparisonExpression($type);
}