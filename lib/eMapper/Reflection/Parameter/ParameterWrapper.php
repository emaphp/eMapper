<?php
namespace eMapper\Reflection\Parameter;

use eMapper\Reflection\Profile\Profiler;
use eMapper\Reflection\Profile\ClassProfile;

/**
 * The ParameterWrapper class defines a wrapper object that encapsulates an array/object and
 * manages access to its keys/properties.
 * @author emaphp
 */
abstract class ParameterWrapper implements \ArrayAccess {
	/**
	 * Wrapper value
	 * @var array|object
	 */
	protected $value;
	
	/**
	 * Parameter map profile
	 * @var ClassProfile
	 */
	protected $parameterMap;
	
	public function __construct($value, $parameterMap = null) {
		$this->value = $value;
	
		if (isset($parameterMap)) {
			//initialize parameter map
			$this->parameterMap = Profiler::getClassProfile($parameterMap);
		}
	}
	
	/**
	 * Obtains wrapped value
	 * @return array|object
	 */
	public function getValue() {
		return $this->value;
	}
	
	/**
	 * Obtains wrapped value parameter map
	 * @return \eMapper\Reflection\Profile\ClassProfile
	 */
	public function getParameterMap() {
		return $this->parameterMap;
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
	 * @throws \UnexpectedValueException
	 */
	public function getPropertyName($property) {
		if (isset($this->parameterMap)) {
			$propertyProfile = $this->parameterMap->getProperty($property);
			
			if ($propertyProfile === false) {
				throw new \UnexpectedValueException(sprintf("Property '%s' was not found in class %s", $this->parameterMap->getReflectionClass()->getName()));
			}
			
			return $propertyProfile->getProperty();
		}
		
		return $property;
	}
	
	/**
	 * Obtains a property associated type
	 * @param string $property
	 * @throws \UnexpectedValueException
	 */
	public function getPropertyType($property) {
		if (isset($this->parameterMap)) {
			$propertyProfile = $this->parameterMap->getProperty($property);
			
			if ($propertyProfile === false) {
				throw new \UnexpectedValueException(sprintf("Property '%s' was not found in class %s", $this->parameterMap->getReflectionClass()->getName()));
			}
			
			return $propertyProfile->getType();
		}
		
		return null;
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