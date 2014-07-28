<?php
namespace eMapper\Query\Predicate;

use eMapper\Engine\Generic\Driver;
use eMapper\Reflection\Profile\ClassProfile;
use eMapper\Query\Field;

abstract class ComparisonPredicate extends SQLPredicate {
	/**
	 * Exprossion for comparison
	 * @var mixed
	 */
	protected $expression;
		
	public function setExpression($expression) {
		$this->expression = $expression;
	}
	
	public function getExpression() {
		return $this->expression;
	}
	
	public function render(Driver $driver) {
		$op = $this->negate ? "NOT" : "";
		return "$op ( %s [? (if (null? (%%0)) 'IS NULL' '= %s') ?])";
	}
	
	public function evaluate(Driver $driver, ClassProfile $profile, &$args, $arg_index = 0) {
		$column = $this->field->getColumnName($profile);
	
		if ($this->expression instanceof Field) {
			$expression = $this->expression->getColumnName($profile);
		}
		else {
			//store expression in argument list
			$index = $this->getArgumentIndex($arg_index);
			$args[$index] = $this->formatExpression($driver, $this->expression);
			
			//build expression
			$expression = $this->buildArgumentExpression($profile, $index, $arg_index);
		}
	
		//build predicate expression
		return sprintf($this->buildComparisonExpression($driver), $column, $expression);
	}
	
	protected function formatExpression(Driver $driver, $expression) {
		return $expression;
	}
	
	protected abstract function buildComparisonExpression(Driver $driver);
}
?>