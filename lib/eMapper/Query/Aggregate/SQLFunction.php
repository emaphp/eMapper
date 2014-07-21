<?php
namespace eMapper\Query\Aggregate;

use eMapper\Query\Field;
use eMapper\Reflection\Profile\ClassProfile;

abstract class SQLFunction {
	/**
	 * Related attribute
	 * @var Field
	 */
	protected $field;
	
	public function __construct(Field $field) {
		$this->field = $field;
	}
	
	public abstract function getExpression(ClassProfile $profile);
}
?>