<?php
namespace eMapper\Reflection\Parameter;

use eMapper\Reflection\Profiler;

class ObjectParameterWrapper extends ParameterWrapper {
	public $classname;
	
	public function __construct($value, $parameterMap = null) {
		parent::__construct($value, $parameterMap);
		$this->classname = get_class($value);
		
		if (isset($parameterMap)) {
			$properties = Profiler::getClassProperties($parameterMap);
			$reflectionClass = ($this->classname == $parameterMap) ? Profiler::getReflectionClass($this->classname) : new \ReflectionClass($value);
			
			foreach ($properties as $name => $annotations) {
				$this->config[$name] = array();
					
				if ($annotations->has('getter')) {
					//validate getter method
					$getter = $annotations->get('getter');
						
					if (!$reflectionClass->hasMethod($getter)) {
						throw new \UnexpectedValueException(sprintf("Getter method '$getter' not found in class %s", get_class($value)));
					}
						
					$method = $reflectionClass->getMethod($getter);
						
					if (!$method->isPublic()) {
						throw new \UnexpectedValueException(sprintf("Getter method '$getter' is not accessible in class %s", get_class($value)));
					}
						
					$this->config[$name]['getter'] = $getter;
				}
				else {
					$property = $annotations->has('property') ? $annotations->get('property') : $name;
				
					if (isset($reflectionClass)) {
						if (!$reflectionClass->hasProperty($property)) {
							throw new \UnexpectedValueException(sprintf("Property '$property' was not found in class %s", get_class($value)));
						}
				
						$rp = $reflectionClass->getProperty($property);
				
						if (!$rp->isPublic()) {
							throw new \UnexpectedValueException(sprintf("Property '$property' is not accessible in class %s", get_class($value)));
						}
					}
					else {
						if (!array_key_exists($property, $value)) {
							throw new \UnexpectedValueException("Key '$property' defined in class {$this->parameterMap} was not found on given parameter");
						}
					}
				
					$this->config[$name]['property'] = $property;
				}
					
				//obtain property type
				if ($annotations->has('type')) {
					$this->config[$name]['type'] = $annotations->get('type');
				}
				elseif ($annotations->has('var')) {
					$this->config[$name]['var'] = $annotations->get('var');
				}
			}
		}
		else {
			$reflectionClass = Profiler::getReflectionClass($this->classname);
			$properties = Profiler::getClassProperties($this->classname);
			
			foreach ($properties as $name => $annotations) {
				//ignore non-public properties
				$property = $reflectionClass->getProperty($name);
				
				if (!$property->isPublic()) {
					continue;
				}
				
				$this->config[$name] = array();
				
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
			return get_object_vars($this->value);
		}
		else {
			$vars = array();
				
			foreach ($this->config as $name => $config) {
				if (array_key_exists('getter', $config)) {
					$getter = $config['getter'];
					$vars[$name] = $this->value->$getter;
				}
				else {
					$property = $config['property'];
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
		if (array_key_exists('getter', $this->config[$offset])) {
			$getter = $this->config[$offset]['getter'];
			return $this->value->$getter();
		}
		elseif (array_key_exists('property', $this->config[$offset])) {
			$property = $this->config[$offset]['property'];
			return $this->value->$property;
		}
		
		return $this->value->$offset;
	}
}
?>