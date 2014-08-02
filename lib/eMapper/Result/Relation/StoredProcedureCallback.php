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
	protected $procedure;
	
	public function getProcedure() {
		return $this->procedure;
	}
	
	protected function parseMetadata(AnnotationsBag $annotations) {
		//obtain procedure name
		$this->procedure = $annotations->get('Procedure')->getValue();
	}
		
	protected function evaluateArgs($row, &$proc_types) {
		$args = [];
		$wrapper = ParameterWrapper::wrapValue($row);
	
		//get class profile
		$profile = Profiler::getClassProfile($this->reflectionProperty->getDeclaringClass()->getName());
	
		foreach ($this->args as $arg) {
			if ($arg instanceof Attr) {
				//get attribute name
				$name = $arg->getName();
				
				//check if the property is available
				if (!$wrapper->offsetExists($name)) {
					throw new \InvalidArgumentException(sprintf("Property '%s' was not found whe evaluating arguments for %s attribute", $arg->getName(), $this->name));
				}
				
				//get attribute value and type
				$value = $wrapper->offsetGet($name);
				$type = $arg->getType();
				
				if (is_null($type)) {
					if (isset($profile->propertiesConfig[$name]->type)) {
						$type = $profile->propertiesConfig[$name]->type;
					}
					else {
						$type = strtolower(gettype($value));
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
		
		if (!array_key_exists('proc.types', $this->config)) {
			$this->config['proc.types'] = $proc_types;
		}
	}
	
	public function evaluate($row, $mapper) {
		//evaluate condition
		if ($this->checkCondition($row, $mapper->getConfig()) === false) {
			return null;
		}
		
		//build argument list
		$proc_types = [];
		$args = $this->evaluateArgs($row, $proc_types);
		
		//apply configuration
		$this->updateConfig($mapper->getConfig(), $proc_types);
		
		//call stored procedure
		return call_user_func([$mapper->merge($this->config), '__call'], $this->procedure, $args);
	}
}
?>