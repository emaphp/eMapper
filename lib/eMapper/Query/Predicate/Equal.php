<?php
namespace eMapper\Query\Predicate;

use eMapper\Query\Field;
use eMapper\Reflection\Profile\ClassProfile;
use eMapper\Engine\Generic\Driver;

class Equal extends ComparisonPredicate {
	protected function comparisonExpression(Driver $driver, &$args, $index) {
		return '%s = %s';
	}
	
	public function evaluate(Driver $driver, ClassProfile $profile, &$args, $arg_index = 0) {
		$column = $this->field->getColumnName($profile);
		
		if ($this->expression instanceof Field) {
			$expression = $this->expression->getColumnName($profile);
		}
		else {
			//store expression in argument list
			$index = $this->getArgumentIndex($arg_index);
			$args[$index] = $this->expression;
			
			//build expression
			$expression = $this->buildArgumentExpression($profile, $index, $arg_index);
		}
		
		//build predicate expression
		$predicate = sprintf($this->comparisonExpression($driver, $args, $index), $column, $expression);
		
		if ($this->negate) { 
			return 'NOT ' . $predicate;
		}
		
		return $predicate;
	}
}
?>