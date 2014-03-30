<?php
namespace eMapper\Result\Relation;

use eMapper\Result\Argument\PropertyReader;
use eMapper\Reflection\Parameter\ParameterWrapper;
use eMapper\Reflection\Profiler;

class StoredProcedureCallback extends DynamicAttribute {
	/**
	 * Class which declares this attribute
	 * @var string
	 */
	public $classname;
	
	/**
	 * Stored procedure name
	 * @var string
	 */
	public $procedure;
	
	public function __construct($name, $attribute, \ReflectionProperty $reflectionProperty, $classname) {
		parent::__construct($name, $attribute, $reflectionProperty);
		$this->classname = $classname;
	}
	
	protected function parseAttribute($attribute) {
		//obtain procedure name
		$this->procedure = $attribute->get('procedure');
	}
	
	/**
	 * Returns a value type
	 * @param mixed $value
	 * @throws \UnexpectedValueException
	 * @return string
	 */
	protected function getType($value, $property) {
		if (is_array($value)) {
			throw new \UnexpectedValueException("Property '$property' is an array and cannot be used as stored procedure argument");
		}
		
		if (is_object($value)) {
			return get_class($value);
		}
		
		return strtolower(gettype($value));
	}
	
	protected function evaluateArgs($row, $parameterMap, &$proc_types) {
		$args = array();
		$wrapper = ParameterWrapper::wrap($row, $parameterMap);
	
		foreach ($this->args as $arg) {
			if ($arg instanceof PropertyReader) {
				$value = $wrapper[$arg->property];
				
				if (!isset($arg->type)) {
					//try getting type from class annotation
					$profile = Profiler::getClassProfile($this->classname);
					
					if (!array_key_exists($arg->property, $profile->propertiesConfig)) {
						throw new \UnexpectedValueException("Property '{$arg->property}' not found in class '{$this->classname}'");
					}
					
					if (isset($profile->propertiesConfig[$arg->property]->type)) {
						$type = $profile->propertiesConfig[$arg->property]->type;
					}
					else {
						//get value type
						$type = $this->getType($value, $arg->property);
					}
				}
				else {
					$type = $arg->type;
				}
				
				$args[] = $value;
				$proc_types[] = $type;
			}
			else {
				$args[] = $arg;
			}
		}
	
		return $args;
	}
	
	protected function applyConfig($config, $proc_types) {
		$this->config['depth.current'] = $config['depth.current'] + 1;
		$this->config['proc.types'] = $proc_types;
	}
	
	public function evaluate($row, $parameterMap, $mapper) {
		//evaluate condition
		if ($this->checkCondition($row, $parameterMap, $mapper->config) === false) {
			return null;
		}
		
		//build argument list
		$proc_types = array();
		$args = $this->evaluateArgs($row, $parameterMap, $proc_types);
		
		//apply configuration
		$this->applyConfig($mapper->config, $proc_types);
		
		//call stored procedure
		return call_user_func([$mapper->merge($this->config), '__call'], $this->procedure, $args);
	}
}
?>