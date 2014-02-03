<?php
namespace eMapper\Result\Relation;

use eMacros\Program\SimpleProgram;
use eMapper\Dynamic\Provider\EnvironmentProvider;
use eMapper\Reflection\Parameter\ParameterWrapper;
use eMapper\Result\Argument\PropertyReader;

class MacroExpression extends DynamicAttribute {
	/**
	 * Attribute program
	 * @var Progra
	 */
	public $program;
	
	public function __construct($name, $attribute) {
		parent::__construct($name, $attribute);
		
		//obtain program source
		$this->program = new SimpleProgram($attribute->get('eval'));
	}
	
	/**
	 * Evaluates all attribute arguments against current instance
	 * @param mixed $row
	 * @return array
	 */
	protected function evaluateArgs($row, $parameterMap) {
		$args = array();
		$wrapper = ParameterWrapper::wrap($row, $parameterMap);
		
		if ($this->useDefaultArgument) {
			$args[] = $wrapper;
		}
	
		foreach ($this->args as $arg) {
			if ($arg instanceof PropertyReader) {
				$args[] = $wrapper[$arg->property];
			}
			else {
				$args[] = $arg;
			}
		}
	
		return $args;
	}
	
	public function evaluate($row, $parameterMap, $mapper) {
		//evaluate condition
		if (isset($this->condition)) {
			$environmentId = $mapper->config['environment.id'];
				
			if (!EnvironmentProvider::hasEnvironment($environmentId)) {
				EnvironmentProvider::buildEnvironment($environmentId, $mapper->config['environment.class']);
			}
				
			$cond = $this->condition->execute(EnvironmentProvider::getEnvironment($environmentId), ParameterWrapper::wrap($row, $parameterMap));
				
			if ((bool) $cond === false) {
				return null;
			}
		}
		
		$args = $this->evaluateArgs($row, $parameterMap);
		$environmentId = $mapper->config['environment.id'];
		
		//obtain environment
		if (!EnvironmentProvider::hasEnvironment($environmentId)) {
			EnvironmentProvider::buildEnvironment($environmentId, $mapper->config['environment.class']);
		}
		
		return $this->program->executeWith(EnvironmentProvider::getEnvironment($environmentId), $args);
	}
}
?>