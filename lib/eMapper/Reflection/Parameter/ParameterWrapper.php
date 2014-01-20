<?php
namespace eMapper\Reflection\Parameter;

use eMapper\Reflection\Profiler;

abstract class ParameterWrapper implements \ArrayAccess {
	/**
	 * Wrapper value
	 * @var mixed
	 */
	public $value;
	
	/**
	 * Value parameter map
	 * @var string | NULL
	 */
	public $parameterMap;
	
	/**
	 * Parameter map configuration
	 * @var string
	 */
	public $config = array();
	
	/**
	 * Generates a new wrapper instance for the given value
	 * @param mixed $value
	 * @param string $parameterMap
	 * @return ParameterWrapper
	 */
	public static function wrap($value, $parameterMap = null) {
		if (is_array($value)) {
			return new ArrayParameterWrapper($value, $parameterMap);
		}
		elseif ($value instanceof \stdClass) {
			return new ArrayParameterWrapper(get_object_vars($value), $parameterMap);
		}
		elseif ($value instanceof \ArrayObject) {
			return new ArrayParameterWrapper($value->getArrayCopy(), $parameterMap);
		}
		else {
			return new ObjectParameterWrapper($value, $parameterMap);
		}
	}
	
	public function __construct($value, $parameterMap) {
		$this->value = $value;
		$this->parameterMap = $parameterMap;
	}
		
	public function getPropertyConfig($property) {
		if (array_key_exists($property, $this->config)) {
			return $this->config[$property];
		}
		
		return false;
	}
	
	public abstract function getParameterVars();
	
	/**
	 * ARRAYACCESS METHODS
	 */
	
	public abstract function offsetExists($offset);
	public abstract function offsetGet($offset);

	public function offsetSet($offset, $value) {
		return;
	}
	
	public function offsetUnset($offset) {
		return;
	}
}
?>