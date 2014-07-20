<?php
namespace eMapper\Reflection\Parameter;

use eMapper\Reflection\Profiler;
use eMapper\Reflection\Profile\ClassProfile;

abstract class ParameterWrapper implements \ArrayAccess {
	/**
	 * Wrapper value
	 * @var mixed
	 */
	public $value;
	
	/**
	 * Parameter map profile
	 * @var ClassProfile
	 */
	public $parameterMap;
	
	public function __construct($value, $parameterMap = null) {
		$this->value = $value;
	
		if (isset($parameterMap)) {
			//initialize parameter map
			$this->parameterMap = Profiler::getClassProfile($parameterMap);
		}
	}
		
	/**
	 * Generates a new wrapper instance for the given value
	 * @param mixed $value
	 * @param string $parameterMap
	 * @return ParameterWrapper
	 */
	public static function wrapValue($value, $parameterMap = null) {
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
			
			//use class as parameter map
			if (is_null($parameterMap) && Profiler::getClassProfile($classname)->isEntity()) {
				$parameterMap = $classname;
			}
			
			return new ObjectParameterWrapper($value, $parameterMap);
		}
		
		throw new \InvalidArgumentException("Parameter is nor an object or array");
	}
	
	/**
	 * Returns the referred property name by the given offset
	 * @param string $offset
	 * @param boolean $strict
	 * @throws \UnexpectedValueException
	 */
	protected function getPropertyName($offset, $strict = true) {
		//check if offset is valid
		if (!array_key_exists($offset, $this->parameterMap->propertiesConfig)) {
			if ($strict) {
				throw new \UnexpectedValueException();
			}
			
			return false;
		}
		
		return $this->parameterMap->propertiesConfig[$offset]->property;
	}
	
	/*
	 * ARRAY ACCESS METHODS
	 */
	
	public abstract function offsetSet($offset, $value);
	public abstract function offsetUnset($offset);
	public abstract function offsetExists($offset);
	public abstract function offsetGet($offset);
	
	/**
	 * Returns wrapped value as an array
	 * @return array
	 */
	public abstract function getValueAsArray();

}
?>