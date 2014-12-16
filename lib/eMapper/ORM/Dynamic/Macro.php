<?php
namespace eMapper\ORM\Dynamic;

use eMapper\Dynamic\Program\DynamicSQLProgram;
use Omocha\AnnotationBag;
use eMapper\Mapper;

/**
 * The Macro class evaluates S-expressions found on dynamic attributes.
 * @author emaphp
 */
class Macro extends DynamicAttribute {
	/**
	 * Program to execute
	 * @var \eMapper\Dynamic\Program\DynamicSQLProgram 
	 */
	protected $program;
	
	protected function parseMetadata(AnnotationBag $propertyAnnotations) {
		$this->program = new DynamicSQLProgram($propertyAnnotations->get('Eval')->getValue());
	}
	
	public function evaluate($row, Mapper $mapper) {
		if ($this->evaluateCondition($row, $mapper->getConfig()) === false)
			return null;
			
		$args = $this->evaluateArguments($row);
		return $this->program->executeWith($this->buildEnvironment($mapper->getConfig()), $args);
	}
}