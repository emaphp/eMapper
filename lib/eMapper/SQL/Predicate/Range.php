<?php
namespace eMapper\SQL\Predicate;

use eMapper\Query\Field;
use eMapper\Engine\Generic\Driver;
use eMapper\Query\Schema;

/**
 * The Range class defines a predicate for values between the specified range.
 * @author emaphp
 */
class Range extends SQLPredicate {
	/**
	 * From expression
	 * @var mixed
	 */
	protected $from;
	
	/**
	 * To expression
	 * @var mixed
	 */
	protected $to;
	
	public function __construct(Field $field, $negate) {
		parent::__construct($field, $negate);
	}
	
	public function setFrom($from) {
		$this->from = $from;
	}
	
	public function getFrom() {
		return $this->from;
	}
	
	public function setTo($to) {
		$this->to = $to;
	}
	
	public function getTo() {
		return $this->to;
	}

	public function evaluate(Driver $driver, Schema &$schema) {
		$column = $schema->translate($this->field, $this->alias);
		
		//from argument
		$from_index = $this->getArgumentIndex(0);
		$schema->addArgument($from_index, $this->from);
		$from_expression = $this->buildArgumentExpression($this->field, $from_index);
		
		//to argument
		$to_index = $this->getArgumentIndex(0);
		$schema->addArgument($to_index, $this->to);
		$to_expression = $this->buildArgumentExpression($this->field, $to_index);
		
		$format = $this->negate ? '%s NOT BETWEEN %s AND %s' : '%s BETWEEN %s AND %s';  
		return sprintf($format, $column, $from_expression, $to_expression);
	}
	
	public function generate(Driver $driver) {
		return $this->negate ? '%s NOT BETWEEN %s AND %s' : '%s BETWEEN %s AND %s';
	}
}