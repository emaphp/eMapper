<?php
namespace eMapper\Reflection\Parameter;

use eMapper\Reflection\Profiler;

class ObjectParameterWrapper extends ParameterWrapper {
	/**
	 * Wrapped value class
	 * @var string
	 */
	public $classname;
	
	public function __construct($value, $parameterMap = null) {
		parent::__construct($value, $parameterMap);
		$this->classname = get_class($value);
		
		$classname = isset($this->parameterMap) ? $this->parameterMap : $this->classname;
		$this->config = Profiler::getClassProfile($classname)->propertiesConfig;
		$reflectionClass = Profiler::getClassProfile($this->classname)->reflectionClass;
		$filtered = array();
		
		foreach ($this->config as $property => $config) {
			if (isset($config->getter)) {
				//validate getter method
				$getter = $config->getter;
					
				if (!$reflectionClass->hasMethod($getter)) {
					throw new \UnexpectedValueException(sprintf("Getter method '$getter' not found in class %s", $classname));
				}
					
				$method = $reflectionClass->getMethod($getter);
					
				if (!$method->isPublic()) {
					throw new \UnexpectedValueException(sprintf("Getter method '$getter' is not accessible in class %s", $classname));
				}
			}
			elseif (is_null($this->parameterMap)) {
				$rp = $reflectionClass->getProperty($config->property);
				
				if (!$rp->isPublic()) {
					$filtered[$config->property] = 0;
				}
			}
			elseif ($this->classname != $this->parameterMap) {
				$property = $config->property;
					
				if (!$reflectionClass->hasProperty($property)) {
					throw new \UnexpectedValueException(sprintf("Property '$property' was not found in class %s", $classname));
				}
				
				$rp = $reflectionClass->getProperty($property);
				
				if (!$rp->isPublic()) {
					throw new \UnexpectedValueException(sprintf("Property '$property' is not accessible in class %s", $classname));
				}
			}
		}
		
		if (!empty($filtered)) {
			$this->config = array_diff_key($this->config, $filtered);
		}
	}
	
	public function getParameterVars() {
		if (is_null($this->parameterMap)) {
			return get_object_vars($this->value);
		}
		else {
			$vars = array();
				
			foreach ($this->config as $name => $config) {
				if (isset($config->getter)) {
					$getter = $config->getter;
					$vars[$name] = $this->value->$getter();
				}
				else {
					$property = $config->property;
					$vars[$name] = $this->value->$property;
				}
			}
				
			return $vars;
		}
	}
	
	public function offsetExists($offset) {
		return array_key_exists($offset, $this->config);
	}
	
	public function offsetGet($offset) {
		if (isset($this->config[$offset]->getter)) {
			$getter = $this->config[$offset]->getter;
			return $this->value->$getter();
		}
		else {
			$property = $this->config[$offset]->property;
			return $this->value->$property;
		}
	}
}
?>