<?php
namespace eMapper\Reflection\Parameter;

use eMapper\Reflection\Profiler;

class ArrayParameterWrapper extends ParameterWrapper {
	public function __construct($value, $parameterMap = null) {
		parent::__construct($value, $parameterMap);
		
		if (isset($parameterMap)) {
			$this->config = Profiler::getClassProfile($parameterMap)->propertiesConfig;
			
			foreach ($this->config as $property => $config) {
				$key = $config->property;
				
				if (!array_key_exists($key, $value)) {
					throw new \UnexpectedValueException("Key '$key' defined in class $parameterMap was not found on given parameter");
				}
			}
		}
		else {
			$this->config = array();
		}
	}
	
	public function getParameterVars() {
		if (is_null($this->parameterMap)) {
			return $this->value;
		}
		else {
			$vars = array();
			
			foreach ($this->config as $name => $config) {
				$key = $config->property;
				$vars[$name] = $this->value[$key];
			}
			
			return $vars;
		}
	}
	
	public function offsetExists($offset) {
		if (is_null($this->parameterMap)) {
			return array_key_exists($offset, $this->value);
		}
		
		return array_key_exists($offset, $this->config);
	}
	
	public function offsetGet($offset) {
		if (is_null($this->parameterMap)) {
			return $this->value[$offset];
		}
		
		$key = $this->config[$offset]->property;
		return $this->value[$key];
	}
}
?>