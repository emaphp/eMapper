<?php
namespace eMapper\Query\Aggregate;

use eMapper\Query\Field;
use eMapper\Reflection\Profile\ClassProfile;

/**
 * A SQLFunction class represents an aggregate SQL function.
 * @author emaphp
 */
abstract class SQLFunction {
	/**
	 * Related attribute
	 * @var Field
	 */
	protected $field;
	
	public function __construct(Field $field) {
		$this->field = $field;
	}
	
	/**
	 * Generates a string expression for the current instance with the given profile
	 * @param ClassProfile $profile
	 * @param string $alias
	 */
	public abstract function getExpression(ClassProfile $profile, $alias = '');
}
?>