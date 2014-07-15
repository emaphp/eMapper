<?php
namespace eMapper\Query\Predicate;

use eMapper\Reflection\Profile\ClassProfile;
use eMapper\Query\Field;
use eMapper\Engine\Generic\Driver;

class Range extends SQLPredicate {
	public function __construct(Field$field, $from, $to) {
		parent::__construct($field);
		$this->from = $from;
		$this->to = $to;
	}
	
	public function evaluate(Driver $driver, ClassProfile $profile, &$args, $arg_index = 0) {
		$column = $this->field->getColumnName($profile);

		//add from argument
		$from_index = $this->getArgumentIndex($arg_index);
		$args[$from_index] = $this->from;
		$from_expression = $this->buildArgumentExpression($profile, $from_index, $arg_index);
		
		//add to argument
		$to_index = $this->getArgumentIndex($arg_index);
		$args[$to_index] = $this->to;
		$to_expression = $this->buildArgumentExpression($profile, $to_index, $arg_index);
		
		if ($this->negate) {
			return sprintf("%s NOT BETWEEN %s AND %s", $column, $from_expression, $to_expression);
		}
		
		return sprintf("%s BETWEEN %s AND %s", $column, $from_expression, $to_expression);
	}
}
?>