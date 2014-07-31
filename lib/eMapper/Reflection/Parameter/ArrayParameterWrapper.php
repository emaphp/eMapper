<?php
namespace eMapper\Reflection\Parameter;

use eMapper\Reflection\Profiler;

/**
 * The ArrayParameterWrapper class provides an interface for accessing array keys.
 * @author emaphp
 */
class ArrayParameterWrapper extends ParameterWrapper {
	public function getValueAsArray() {
		if (isset($this->parameterMap)) {
			$value = [];
			
			foreach ($this->parameterMap->propertiesConfig as $property => $config) {
				$key = $config->property;
				
				if (array_key_exists($key, $this->value)) {
					$value[$property] = $this->value[$key];
				}
			}
			
			return $value;
		}
		
		return $this->value;
	}
	
	/*
	 * ARRAY ACCESS METHODS
	 */
	
	public function offsetSet($offset, $value) {
		if (isset($this->parameterMap)) {
			$key = $this->getPropertyName($offset);
			$this->value[$key] = $value;
		}
		else {
			$this->value[$offset] = $value;
		}
	}
	
	public function offsetUnset($offset) {
		if (isset($this->parameterMap)) {
			$key = $this->getPropertyName($offset, false);
			
			if ($key === false) {
				return;
			}
			
			unset($this->value[$key]);
		}
		elseif (array_key_exists($offset, $this->value)) {
			unset($this->value[$offset]);
		}
	}
	
	public function offsetExists($offset) {
		if (isset($this->parameterMap)) {
			$key = $this->getPropertyName($offset, false);
			
			if ($key === false) {
				return false;
			}
			
			return array_key_exists($key, $this->value);
		}
		
		return array_key_exists($offset, $this->value);
	}
	
	public function offsetGet($offset) {
		if (isset($this->parameterMap)) {
			$key = $this->getPropertyName($offset);
			return $this->value[$key];
		}
		
		if (!array_key_exists($offset, $this->value)) {
			throw new \UnexpectedValueException();
		}

		return $this->value[$offset];
	}
}
?>