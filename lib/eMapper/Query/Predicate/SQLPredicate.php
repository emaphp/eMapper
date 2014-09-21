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
			return self::$counter++;
		}
	
		return 'arg' . self::$counter++;
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
			
			if ($property === false) {
				return null;
			}
			
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
		static $counter = 0;
		
		//get field associations and referred profile
		list($associations, $target) = $field->getAssociations($profile);
		
		//check if specified field is a plain one (Ex: name)
		if (is_null($associations)) {
			return empty($this->alias) ? $field->getColumnName($profile) : $this->alias . '.' . $field->getColumnName($profile);
		}
		
		//field is associated (Ex: profile__name)
		$diff = array_keys(array_diff_key($associations, $joins));
		
		//check is association already loaded
		//key is a string like assoc1 or assoc1__assoc2
		foreach ($diff as $key) {
			$join = $associations[$key];
			
			//build table alias
			$join->setAlias('_t' . $counter++);
			
			//set join parent (if any)
			$parent = $join->getParentName();
			
			if (!is_null($parent)) {
				$join->setParent($joins[$parent]);
			}
			
			//add join to list
			$joins[$key] = $join;
		}
		
		//append alias to column name
		$alias = $joins[$field->getFullPath()]->getAlias();
		$field = Attr::__callstatic($field->getName());
		return $alias . '.' . $field->getColumnName($target);
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
}
?>