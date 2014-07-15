<?php
namespace eMapper\Query\Predicate;

use eMapper\Reflection\Profile\ClassProfile;
use eMapper\Engine\Generic\Driver;
use eMapper\Query\Field;

abstract class SQLPredicate {
	/**
	 * Predicate field
	 * @var Field
	 */
	protected $field;
	
	/**
	 * Indicates if the predicate must be negated
	 * @var boolean
	 */
	protected $negate = false;
	
	/**
	 * Argument counter (used for additional parameters in query)
	 * @var integer
	 */
	protected static $counter = 0;
	
	public function __construct(Field $field) {
		$this->field = $field;
	}
	
	/**
	 * Obtains an index for the current argument
	 * @param integer $arg_index
	 * @return number|string
	 */
	protected function getArgumentIndex($arg_index) {
		if ($arg_index != 0) {
			return self::argNumber();
		}
	
		return 'arg' . self::argNumber();
	}
	
	/**
	 * Obtains the current field type
	 * @param ClassProfile $profile
	 * @return string|NULL
	 */
	protected function getFieldType(ClassProfile $profile) {
		if ($this->field->hasType()) {
			return $this->field->getType();
		}
		
		return $profile->getFieldType($this->field->getName());
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
	
	public abstract function evaluate(Driver $driver, ClassProfile $profile, &$args, $arg_index = 0);
	
	public static function argNumber() {
		return self::$counter++;
	}
}
?>