<?php
namespace eMapper\Reflection\Parameter;

use eMapper\Reflection\Profiler;

class ArrayParameterWrapper extends ParameterWrapper {
	public function __construct($value, $parameterMap = null) {
		parent::__construct($value, $parameterMap);
		
		if (isset($parameterMap)) {
			$properties = Profiler::getClassProperties($parameterMap);
			
			foreach ($properties as $name => $annotations) {
				$this->config[$name] = array();
				
				//get references key
				$property = $annotations->has('property') ? $annotations->get('property') : $name;
				
				if (!array_key_exists($property, $value)) {
					throw new \UnexpectedValueException("Key '$property' defined in class $parameterMap was not found on given parameter");
				}
				
				$this->config[$name]['property'] = $property;
				
				//obtain property type
				if ($annotations->has('type')) {
					$this->config[$name]['type'] = $annotations->get('type');
				}
				elseif ($annotations->has('var')) {
					$this->config[$name]['var'] = $annotations->get('var');
				}
			}
		}
	}
	
	public function getParameterVars() {
		if (is_null($this->parameterMap)) {
			return $this->value;
		}
		else {
			$vars = array();
			
			foreach ($this->config as $name => $config) {
				$key = $config['property'];
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
		
		$key = $this->config[$offset]['property'];
		return $this->value[$key];
	}
}
?>