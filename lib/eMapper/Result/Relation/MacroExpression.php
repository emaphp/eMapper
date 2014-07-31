<?php
namespace eMapper\Result\Relation;

use eMacros\Program\SimpleProgram;
use eMapper\Reflection\Parameter\ParameterWrapper;
use eMapper\Annotations\AnnotationsBag;
use eMapper\Query\Attr;

/**
 * The MacroExpression class provides the logic for macro attributes en entity classes.
 * @author emaphp
 */
class MacroExpression extends DynamicAttribute {
	/**
	 * Attribute program
	 * @var Progra
	 */
	protected $program;
	
	public function getProgram() {
		return $this->program;
	}
	
	/* (non-PHPdoc)
	 * @see \eMapper\Result\Relation\DynamicAttribute::parseAttribute()
	 */
	protected function parseMetadata(AnnotationsBag $annotations) {
		//obtain program source
		$this->program = new SimpleProgram($annotations->get('Eval')->getValue());
	}
	
	/**
	 * Evaluates all attribute arguments against current instance
	 * @param mixed $row
	 * @return array
	 */
	protected function evaluateArgs($row, $parameterMap) {
		$args = [];
		$wrapper = ParameterWrapper::wrapValue($row, $parameterMap);
		
		if ($this->useDefaultArgument) {
			$args[] = $wrapper;
		}
	
		foreach ($this->args as $arg) {
			$args[] = $arg instanceof Attr ? $wrapper[$arg->getName()] : $arg;
		}
	
		return $args;
	}
	
	public function evaluate($row, $parameterMap, $mapper) {
		//evaluate condition
		if ($this->checkCondition($row, $parameterMap, $mapper->config) === false) {
			return null;
		}
		
		$args = $this->evaluateArgs($row, $parameterMap);
		return $this->program->executeWith($this->buildEnvironment($mapper->config), $args);
	}
}
?>