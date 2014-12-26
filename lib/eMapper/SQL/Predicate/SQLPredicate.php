<?php
namespace eMapper\SQL\Predicate;

use eMapper\Reflection\ClassProfile;
use eMapper\Engine\Generic\Driver;
use eMapper\Query\Field;
use eMapper\Query\Attr;
use eMapper\Query\Schema;

/**
 * A SQLPredicate class encapsulates the generic behaviour defined for query conditional clauses.
 * @author emaphp
 */
abstract class SQLPredicate {
	/**
	 * Predicate field
	 * @var \eMapper\Query\Field
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
	
	public function __construct(Field $field, $negate = false) {
		$this->field = $field;
		$this->negate = $negate;
	}
	
	public function setAlias($alias) {
		$this->alias = $alias;
	}
	
	public function getAlias() {
		return $this->alias;
	}
	
	public function getField() {
		return $this->field;
	}
	
	public function getNegate() {
		return $this->negate;
	}
	
	/**
	 * Obtains an index for the current argument
	 * @return string
	 */
	protected static function getArgumentIndex() {
		static $counter = 0;
		return '$' . $counter++;
	}
		
	/**
	 * Returns an expression for a given argument index
	 * @param \eMapper\Query\Field $field
	 * @param string $index
	 * @return string
	 */
	public function buildArgumentExpression(Field $field, $index) {
		$type = $field->getType();
		if (isset($type))
			return '#{' . "$index:$type" . '}';
		return '#{' . $index . '}';
	}
	
	/**
	 * Evaluates a SQLPredicate getting any additional arguments
	 * @param \eMapper\Engine\Generic\Driver $driver
	 * @param \eMapper\Query\Schema $schema
	 */
	public abstract function evaluate(Driver $driver, Schema &$schema);
	
	/**
	 * Renders a SQLPredicate to the corresponding Dynamic SQL expression
	 * @param \eMapper\Engine\Generic\Driver $driver
	 */
	public abstract function generate(Driver $driver);
}