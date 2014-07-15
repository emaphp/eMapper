<?php
namespace eMapper\Engine\Generic\Regex;

abstract class GenericRegex {
	/**
	 * Indicates if the regular expression is case sensitive
	 * @var boolean
	 */
	protected $case_sensitive;
	
	public function __construct($case_sensitive) {
		$this->case_sensitive = $case_sensitive;
	}
	
	public abstract function filter($expression);
	public abstract function comparisonExpression($negate);
}
?>