<?php
namespace eMapper\Query\Predicate;

use eMapper\Engine\Generic\Driver;
use eMapper\Reflection\Profile\ClassProfile;

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
	
	protected abstract function comparisonExpression(Driver $driver, &$args, $index);
}
?>