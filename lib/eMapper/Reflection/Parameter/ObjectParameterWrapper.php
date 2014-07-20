<?php
namespace eMapper\Reflection\Parameter;

use eMapper\Reflection\Profiler;
use eMapper\Reflection\Profile\ClassProfile;

class ObjectParameterWrapper extends ParameterWrapper {
	/**
	 * Class profile
	 * @var ClassProfile
	 */
	public $profile;
	
	public function __construct($value, $parameterMap = null) {
		parent::__construct($value, $parameterMap);
		$this->profile = Profiler::getClassProfile(get_class($value));
	}
	
	public function getValueAsArray() {
		if (isset($this->parameterMap)) {
			$value = [];
			
			foreach ($this->parameterMap->propertiesConfig as $property => $name) {
				$propertyName = $config->property;
				
				//check if the property is available
				if (array_key_exists($propertyName, $this->profile->propertiesConfig)) {
					$reflectionProperty = $this->profile->propertiesConfig[$propertyName]->reflectionProperty;
					$value[$property] = $reflectionProperty->getValue($this->value);
				}
			}
			
			return $value;
		}
		
		return get_object_vars($this->value);
	}
	
	/*
	 * ARRAY ACCESS METHODS
	 */
	
	public function offsetSet($offset, $value) {
		if (isset($this->parameterMap)) {
			//get referred property
			$propertyName = $this->getPropertyName($offset);
			
			//check if the property is available
			if (!array_key_exists($propertyName, $this->profile->propertiesConfig)) {
				throw new \RuntimeException();
			}
			
			//set value
			$reflectionProperty = $this->profile->propertiesConfig[$propertyName]->reflectionProperty;
			$reflectionProperty->setValue($this->value, $value);
		}
		else {
			//check if the property is available
			if (!array_key_exists($offset, $this->profile->propertiesConfig)) {
				throw new \UnexpectedValueException();
			}
			
			//set value
			$reflectionProperty = $this->profile->propertiesConfig[$offset]->reflectionProperty;
			$reflectionProperty->setValue($this->value, $value);
		}
	}
	
	public function offsetUnset($offset) {
		if (isset($this->parameterMap)) {
			//get referred property
			$propertyName = $this->getPropertyName($offset, false);
			
			if ($propertyName === false) {
				return;
			}
			
			//check if the property is available
			if (!array_key_exists($propertyName, $this->profile->propertiesConfig)) {
				return;
			}
				
			//set value
			$reflectionProperty = $this->profile->propertiesConfig[$propertyName]->reflectionProperty;
			$reflectionProperty->setValue($this->value, NULL);
		}
		else {
			//check if the property is available
			if (!array_key_exists($offset, $this->profile->propertiesConfig)) {
				return;
			}
			
			//set value
			$reflectionProperty = $this->profile->propertiesConfig[$offset]->reflectionProperty;
			$reflectionProperty->setValue($this->value, null);
		}
	}
	
	public function offsetExists($offset) {
		if (isset($this->parameterMap)) {
			//get referred property
			$propertyName = $this->getPropertyName($offset, false);
			
			if ($propertyName === false) {
				return false;
			}
			
			return array_key_exists($propertyName, $this->profile->propertiesConfig);
		}
		
		return array_key_exists($offset, $this->profile->propertiesConfig);
	}
	
	public function offsetGet($offset) {
		if (isset($this->parameterMap)) {
			//get referred property
			$propertyName = $this->getPropertyName($offset);
			
			//check if the property is available
			if (!array_key_exists($propertyName, $this->profile->propertiesConfig)) {
				throw new \RuntimeException();
			}
			
			//set value
			$reflectionProperty = $this->profile->propertiesConfig[$propertyName]->reflectionProperty;
			return $reflectionProperty->getValue($this->value);
		}
		else {
			//check if the property is available
			if (!array_key_exists($offset, $this->profile->propertiesConfig)) {
				throw new \UnexpectedValueException();
			}
			
			//set value
			$reflectionProperty = $this->profile->propertiesConfig[$offset]->reflectionProperty;
			return $reflectionProperty->getValue($this->value);
		}
	}
}
?>