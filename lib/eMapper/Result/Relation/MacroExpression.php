<?php
namespace eMapper\Result\Relation;

use eMacros\Program\SimpleProgram;
use eMapper\Dynamic\Provider\EnvironmentProvider;

class MacroExpression extends DynamicAttribute {
	/**
	 * Attribute program
	 * @var string
	 */
	public $program;
		
	public function __construct($attribute) {
		parent::__construct($attribute);
		
		//obtain program source
		$this->program = new SimpleProgram($attribute->get('eval'));
	}
	
	public function evaluate($row, $mapper) {
		$args = $this->evaluateArgs($row);
		$environmentId = $mapper->config['environment.id'];
		
		//obtain environment
		if (!EnvironmentProvider::hasEnvironment($environmentId)) {
			EnvironmentProvider::buildEnvironment($environmentId,
					$mapper->config['environment.class'],
					$mapper->config['environment.import']);
		}
		
		return $this->program->executeWith(EnvironmentProvider::getEnvironment($environmentId), $args);
	}
}
?>