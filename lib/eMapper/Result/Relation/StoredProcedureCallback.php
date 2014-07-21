<?php
namespace eMapper\Result\Relation;

use eMapper\Reflection\Parameter\ParameterWrapper;
use eMapper\Reflection\Profiler;
use eMapper\Annotations\AnnotationsBag;
use eMapper\Query\Attr;

class StoredProcedureCallback extends DynamicAttribute {
	/**
	 * Stored procedure name
	 * @var string
	 */
	public $procedure;
	
	protected function parseMetadata(AnnotationsBag $annotations) {
		//obtain procedure name
		$this->procedure = $annotations->get('Procedure')->getValue();
	}
		
	protected function evaluateArgs($row, $parameterMap, &$proc_types) {
		$args = [];
		$wrapper = ParameterWrapper::wrapValue($row, $parameterMap);
	
		//get class profile
		$classname = $this->reflectionProperty->getDeclaringClass()->getName();
		$profile = Profiler::getClassProfile($classname);
	
		foreach ($this->args as $arg) {
			if ($arg instanceof Attr) {
				//get attribute name
				$name = $arg->getName();
				
				//check if property is declared
				if (!in_array($name, $profile->columnNames)) {
					throw new \RuntimeException();
				}
				
				//get attribute value and type
				$value = $wrapper[$name];
				$type = $arg->getType();
				
				if (is_null($type)) {
					if (isset($profile->propertiesConfig[$name]->type)) {
						$type = $profile->propertiesConfig[$name]->type;
					}
					else {
						//determine type by original value
						if (is_array($value)) {
							throw new \RuntimeException();
						}
						
						if (is_object($value)) {
							$type = get_class($value);
						}
						else {
							strtolower(gettype($value));
						}
					}
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
	
	protected function updateConfig($config, $proc_types) {
		$this->config['depth.current'] = $config['depth.current'] + 1;
		$this->config['proc.types'] = $proc_types;
	}
	
	public function evaluate($row, $parameterMap, $mapper) {
		//evaluate condition
		if ($this->checkCondition($row, $parameterMap, $mapper->config) === false) {
			return null;
		}
		
		//build argument list
		$proc_types = [];
		$args = $this->evaluateArgs($row, $parameterMap, $proc_types);
		
		//apply configuration
		$this->updateConfig($mapper->config, $proc_types);
		
		//call stored procedure
		return call_user_func([$mapper->merge($this->config), '__call'], $this->procedure, $args);
	}
}
?>