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
	 * @var array
	 */
	public $config;
	
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
		elseif (is_object($value)) {
			$classname = get_class($value);
			
			if (is_null($parameterMap) && Profiler::getClassProfile($classname)->isEntity()) {
				$parameterMap = $classname;
			}
			
			return new ObjectParameterWrapper($value, $parameterMap);
		}
		
		throw new \InvalidArgumentException("Parameter is nor an object or array");
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
	
	public function offsetSet($offset, $value) {
		return;
	}
	
	public function offsetUnset($offset) {
		return;
	}
	
	public abstract function offsetExists($offset);
	public abstract function offsetGet($offset);
	public abstract function getParameterVars();

}
?>