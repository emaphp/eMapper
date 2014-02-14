<?php
namespace eMapper\Result\Relation;

use eMapper\Result\Argument\PropertyReader;
use eMapper\Reflection\Parameter\ParameterWrapper;
use eMapper\Reflection\Profiler;

class StoredProcedureCallback extends DynamicAttribute {
	/**
	 * Stored procedure name
	 * @var string
	 */
	public $procedure;
	
	public function __construct($classname, $name, $attribute) {
		parent::__construct($classname, $name, $attribute);
		
		//obtain procedure name
		$this->procedure = $attribute->get('procedure');
	}
	
	/**
	 * Returns a value type
	 * @param mixed $value
	 * @throws \UnexpectedValueException
	 * @return string
	 */
	protected function getType($value) {
		if (is_array($value)) {
			throw new \UnexpectedValueException("Type 'array' cannot be used as stored procedure argument");
		}
		
		if (is_object($value)) {
			return get_class($value);
		}
		
		return strtolower(gettype($value));
	}
	
	protected function evaluateArgs($row, $parameterMap, &$sptypes) {
		$args = array();
		$wrapper = ParameterWrapper::wrap($row, $parameterMap);
	
		foreach ($this->args as $arg) {
			if ($arg instanceof PropertyReader) {
				$value = $wrapper[$arg->property];
				
				if (!isset($arg->type)) {
					//try getting type from class annotation
					$profile = Profiler::getClassProfile($this->classname);
					
					if (!array_key_exists($arg->property, $profile->propertiesConfig)) {
						throw new \UnexpectedValueException();
					}
					
					if (isset($profile->propertiesConfig[$arg->property]->type)) {
						$type = $profile->propertiesConfig[$arg->property]->type;
					}
					else {
						//get value type
						$type = $this->getType($value);
					}
				}
				else {
					$type = $arg->type;
				}
				
				$args[] = $value;
				$sptypes[] = $type;
			}
			else {
				$args[] = $arg;
			}
		}
	
		return $args;
	}
	
	protected function mergeConfig($config, $sptypes) {
		$this->config['depth.current'] = $config['depth.current'] + 1;
		$this->config['procedure.types'] = $sptypes;
	}
	
	public function evaluate($row, $parameterMap, $mapper) {
		//evaluate condition
		if ($this->checkCondition($row, $parameterMap, $mapper->config) === false) {
			return null;
		}
		
		//build argument list
		$sptypes = array();
		$args = $this->evaluateArgs($row, $parameterMap, $sptypes);
		
		//merge mapper configuration
		$this->mergeConfig($mapper->config, $sptypes);
		
		//call stored procedure
		return call_user_func(array($mapper->merge($this->config), '__call'), $this->procedure, $args);
	}
}
?>