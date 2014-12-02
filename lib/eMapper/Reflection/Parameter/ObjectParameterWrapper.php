<?php
namespace eMapper\Reflection\Parameter;

use eMapper\Reflection\Profiler;

/**
 * The ObjectParameterWrapper class provides an interface for accessing properties.
 * @author emaphp
 */
class ObjectParameterWrapper extends ParameterWrapper {
	/**
	 * Class profile
	 * @var ClassProfile
	 */
	protected $profile;
	
	public function __construct($value, $parameterMap = null) {
		parent::__construct($value, $parameterMap);
		$this->profile = Profiler::getClassProfile(get_class($value));
	}
	
	/**
	 * Obtains current value class profile
	 * @return \eMapper\Reflection\Profile\ClassProfile
	 */
	public function getProfile() {
		return $this->profile;
	}
	
	public function getValueAsArray() {
		if (isset($this->parameterMap)) {
			$value = [];
			
			foreach ($this->parameterMap->getProperties() as $name => $propertyProfile) {
				$property = $this->profile->getProperty($propertyProfile->getProperty());
				
				if ($property !== false)
					$value[$name] = $property->getReflectionProperty()->getValue($this->value);
			}
			
			return $value;
		}
		
		return get_object_vars($this->value);
	}
	
	/**
	 * Obtains a property associated type
	 * @param string $property
	 * @throws \UnexpectedValueException
	 */
	public function getPropertyType($property) {
		if (isset($this->parameterMap)) {
			$propertyProfile = $this->parameterMap->getProperty($property);
				
			if ($propertyProfile === false)
				throw new \UnexpectedValueException(sprintf("Property '%s' was not found in class %s", $this->parameterMap->getReflectionClass()->getName()));
				
			return $propertyProfile->getType();
		}
		elseif ($this->profile->isEntity()) {
			$propertyProfile = $this->profile->getProperty($property);
			
			if ($propertyProfile === false)
				throw new \UnexpectedValueException(sprintf("Property '%s' was not found in class %s", $this->profile->getReflectionClass()->getName()));
			
			return $propertyProfile->getType();
		}
	
		return null;
	}
	
	/*
	 * ARRAY ACCESS METHODS
	 */
	
	public function offsetSet($offset, $value) {
		if (isset($this->parameterMap)) {
			//get referred property
			$propertyProfile = $this->profile->getProperty($this->getPropertyName($offset));
			
			if ($propertyProfile === false)
				throw new \RuntimeException(sprintf("Property %s not found id class %s", $propertyName, $this->profile->getReflectionClass()->getName()));
			
			//set value
			$reflectionProperty = $propertyProfile->getReflectionProperty();
			$reflectionProperty->setValue($this->value, $value);
		}
		else {
			$propertyProfile = $this->profile->getProperty($offset);
			
			if ($propertyProfile === false)
				throw new \RuntimeException(sprintf("Property %s not found id class %s", $offset, $this->profile->getReflectionClass()->getName()));
			
			//set value
			$reflectionProperty = $propertyProfile->getReflectionProperty();
			$reflectionProperty->setValue($this->value, $value);
		}
	}
	
	public function offsetUnset($offset) {
		if (isset($this->parameterMap)) {
			//get referred property
			$propertyProfile = $this->profile->getProperty($this->getPropertyName($offset));
			if ($propertyProfile === false) return;

			//set value
			$reflectionProperty = $propertyProfile->getReflectionProperty();
			$reflectionProperty->setValue($this->value, null);
		}
		else {
			$propertyProfile = $this->profile->getProperty($offset);	
			if ($propertyProfile === false) return;
			
			//set value
			$reflectionProperty = $propertyProfile->getReflectionProperty();
			$reflectionProperty->setValue($this->value, null);
		}
	}
	
	public function offsetExists($offset) {
		if (isset($this->parameterMap)) {
			$propertyName = $this->getPropertyName($offset);			
			if ($propertyName === false) return false;
			
			//get referred property
			$propertyProfile = $this->profile->getProperty($propertyName);			
			return $propertyProfile != false;
		}
		
		return $this->profile->hasProperty($offset);
	}
	
	public function offsetGet($offset) {
		if (isset($this->parameterMap)) {
			$propertyName = $this->getPropertyName($offset);
			
			if ($propertyName === false)
				throw new \RuntimeException(sprintf("Property %s not found in class %s", $propertyName, $this->parameterMap->getReflectionClass()->getName()));
			
			//get referred property
			$propertyProfile = $this->profile->getProperty($propertyName);
			
			if ($propertyProfile === false)
				throw new \RuntimeException(sprintf("Property %s not found id class %s", $propertyName, $this->profile->getReflectionClass()->getName()));
			
			//set value
			$reflectionProperty = $propertyProfile->getReflectionProperty();
			return $reflectionProperty->getValue($this->value);
		}
		
		$propertyProfile = $this->profile->getProperty($offset);
		
		if ($propertyProfile === false)
			throw new \RuntimeException(sprintf("Property %s not found id class %s", $offset, $this->profile->getReflectionClass()->getName()));
		
		//set value
		$reflectionProperty = $propertyProfile->getReflectionProperty();
		return $reflectionProperty->getValue($this->value);
	}
}
?>