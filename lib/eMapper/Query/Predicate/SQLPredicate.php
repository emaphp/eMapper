<?php
namespace eMapper\Query\Predicate;

use eMapper\Reflection\Profile\ClassProfile;
use eMapper\Engine\Generic\Driver;
use eMapper\Query\Field;
use eMapper\Query\Attr;

/**
 * A SQLPredicate class encapsulates the generic behaviour defined for query conditional clauses.
 * @author emaphp
 */
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
	protected $negate;
	
	/**
	 * Target table alias
	 * @var string
	 */
	protected $alias;
	
	/**
	 * Argument counter (used for additional parameters in query)
	 * @var integer
	 */
	protected static $counter = 0;
	
	public function __construct(Field $field, $negate = false) {
		$this->field = $field;
		$this->negate = $negate;
	}
	
	public function setAlias($alias) {
		$this->alias = $alias;
	}
	
	public function getField() {
		return $this->field;
	}
	
	public function getNegate() {
		return $this->negate;
	}
	
	/**
	 * Obtains an index for the current argument
	 * @param integer $arg_index
	 * @return number|string
	 */
	protected static function getArgumentIndex($arg_index) {
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
		
		if ($this->field instanceof Attr) {
			$property = $profile->getProperty($this->field->getName());
			return $property->getType();
		}
		
		return null;
	}
	
	/**
	 * Builds an argument expression for the current query
	 * @param ClassProfile $profile
	 * @param mixed $index Argument relative index
	 * @param unknown $arg_index Argument global index
	 * @return string
	 */
	protected function buildArgumentExpression(ClassProfile $profile, $index, $arg_index) {
		if ($arg_index != 0) {
			//check type
			$type = $this->getFieldType($profile);
				
			//build expression
			if (isset($type)) {
				return '%{' . $arg_index . "[$index]" . ":$type}";
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

	protected function getColumnName(Field $field, ClassProfile $profile, &$joins) {
		if ($field instanceof Attr) {
			list($associations, $target) = $field->getAssociations($profile);
	
			if (!is_null($associations)) {
				$diff = array_diff_key($associations, $joins);
	
				foreach ($diff as $key) {
					$joins[$key] = [$associations[$key], '_t' . self::getArgumentIndex(1)];
				}
	
				list($_, $alias) = $joins[$field->getFullPath()];
				$field = Attr::__callstatic($field->getName());
				$column = $alias . '.' . $field->getColumnName($target);
			}
			else {
				$column = empty($this->alias) ? $field->getColumnName($profile) : $this->alias . '.' . $field->getColumnName($profile);
			}
		}
		else {
			$column = empty($this->alias) ? $field->getColumnName($profile) : $this->alias . '.' . $field->getColumnName($profile);
		}
		
		return $column;
	}
	
	/**
	 * Evaluates a SQLPredicate getting any additional arguments
	 * @param Driver $driver
	 * @param ClassProfile $profile
	 * @param array $joins
	 * @param array $args
	 * @param number $arg_index
	 */
	public abstract function evaluate(Driver $driver, ClassProfile $profile, &$joins, &$args, $arg_index = 0);
	
	/**
	 * Renders a SQLPredicate to the corresponding Dynamic SQL expression
	 * @param Driver $driver
	 */
	public abstract function render(Driver $driver);
	
	/**
	 * Incremental value generator
	 * @return number
	 */
	public static function argNumber() {
		return self::$counter++;
	}
}
?>