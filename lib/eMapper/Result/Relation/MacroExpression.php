<?php
namespace eMapper\Result\Relation;

use eMacros\Program\SimpleProgram;
use eMapper\Dynamic\Provider\EnvironmentProvider;
use eMapper\Reflection\Parameter\ParameterWrapper;

class MacroExpression extends DynamicAttribute {
	/**
	 * Attribute program
	 * @var string
	 */
	public $program;
	
	public function __construct($attribute, $parameterMap = null) {
		parent::__construct($attribute, $parameterMap);
		
		//obtain program source
		$this->program = new SimpleProgram($attribute->get('eval'));
	}
	
	/**
	 * Evaluates all attribute arguments against current instance
	 * @param ParameterWrapper $row
	 * @return array
	 */
	protected function evaluateArgs($row) {
		$args = array();
		$wrapper = ParameterWrapper::wrap($row, $this->parameterMap);
		
		if ($this->useDefaultArgument) {
			$args = $wrapper;
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
	
	public function evaluate($row, $mapper) {
		$args = $this->evaluateArgs($row);
		$environmentId = $mapper->config['environment.id'];
		
		//obtain environment
		if (!EnvironmentProvider::hasEnvironment($environmentId)) {
			EnvironmentProvider::buildEnvironment($environmentId, $mapper->config['environment.class']);
		}
		
		return $this->program->executeWith(EnvironmentProvider::getEnvironment($environmentId), $args);
	}
}
?>