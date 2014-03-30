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
		
		//validate parameter map
		if ($this->classname != $this->parameterMap) {
			foreach ($this->config as $name => $config) {
				$property = $config->property;
				
				if (!$reflectionClass->hasProperty($property)) {
					throw new \UnexpectedValueException(sprintf("Property '$property' was not found in class %s", $classname));
				}
				
				//override reflection property with hte original one
				$this->config[$name]->reflectionProperty = $reflectionClass->getProperty($property);
			}
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
		$property = $this->config[$offset]->property;

		if (!$this->config[$offset]->reflectionProperty->isPublic()) {
			$rp = new \ReflectionProperty($this->value, $property);
			$rp->setAccessible(true);
			return $rp->getValue($this->value);
		}
		else {
			return $this->value->$property;
		}
	}
}
?>