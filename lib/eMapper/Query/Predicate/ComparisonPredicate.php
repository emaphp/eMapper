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
	
	public function __construct(Field $field, $expression) {
		parent::__construct($field);
		$this->expression = $expression;	
	}
	
	protected function buildArgumentExpression(ClassProfile $profile, $index, $arg_index) {
		if ($arg_index != 0) {			
			//check type
			$type = $this->getFieldType($profile);
			
			//build expression
			if (isset($type)) {
				return '%{' . $arg_index . "[$index:$type]" . '}';
			}
			
			return '%{' . $arg_index . "[$index]" . '}';
		}
		
		//check type
		$type = $this->getFieldType($profile);
		
		//build expression
		if (isset($type)) {
			return '#{' . "$index:$type" . '}';
		}
		
		return '#{' . $index . '}';
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
		$predicate = sprintf($this->buildComparisonExpression($driver), $column, $expression);
	
		if ($this->negate) {
			return 'NOT ' . $predicate;
		}
	
		return $predicate;
	}
	
	protected abstract function formatExpression(Driver $driver, $expression);
	protected abstract function buildComparisonExpression(Driver $driver);
}
?>