<?php
namespace eMapper\SQL\Predicate;

use eMapper\Engine\Generic\Driver;
use eMapper\Query\Field;
use eMapper\Query\Schema\Schema;

/**
 * The ComparisonPredicate class adds an expression property which is used for comparison.
 * @author emaphp
 */
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
	
	public function generate(Driver $driver) {
		$op = $this->negate ? "NOT" : "";
		$eq_op = $this->negate ? '<>' : '=';
		return "%s [? (if (null? (%%0)) 'IS $op NULL' '$eq_op %s') ?]";
	}
	
	public function evaluate(Driver $driver, Schema &$schema) {
		$column = $schema->translate($this->field, $this->alias);
		
		if ($this->expression instanceof Field)
			$expression = $schema->translate($this->expression, $this->alias);
		else {
			$index = $this->getArgumentIndex(0);
			$schema->addArgument($index, $this->formatExpression($driver, $this->expression));
			$expression = $this->buildArgumentExpression($this->field, $index);
		}
		
		return sprintf($this->buildComparisonExpression($driver), $column, $expression);
	}
	
	/**
	 * Formats a expression for the current comparison predicate
	 * @param \eMapper\Engine\Generic\Driver $driver
	 * @param mixed $expression
	 * @return mixed
	 */
	protected function formatExpression(Driver $driver, $expression) {
		return $expression;
	}
	
	/**
	 * Obtains a string expression containing the comparison predicate
	 * @param \eMapper\Engine\Generic\Driver $driver
	 */
	protected abstract function buildComparisonExpression(Driver $driver);
}