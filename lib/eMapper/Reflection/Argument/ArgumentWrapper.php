<?php
namespace eMapper\Reflection\Argument;

/**
 * The ArgumentWrapper class defines a wrapper object that encapsulates an array/object and manages access to its keys/properties.
 * @author emaphp
 */
abstract class ArgumentWrapper implements \ArrayAccess {
	/**
	 * Value to wrap
	 * @var array | object
	 */
	protected $value;
	
	protected function __construct($value) {
		$this->value = $value;
	}
	
	public static function wrap($value) {
		if (is_array($value) || $value instanceof \ArrayObject)
			return new ArrayArgumentWrapper($value);
		if ($value instanceof \stdClass)
			return new ArrayArgumentWrapper((array)$value);
		if (is_object($value))
			return new ObjectArgumentWrapper($value);
		
		throw new \InvalidArgumentException(sprintf("ArgumentWrapper::wrap expected an array or object argument but %s was received", gettype($value)));
	}
}
