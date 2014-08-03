<?php
namespace eMapper\Reflection\Profile\Dynamic;

use eMapper\Annotations\AnnotationsBag;
use eMapper\Dynamic\Program\DynamicSQLProgram;

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
	
	/* (non-PHPdoc)
	 * @see \eMapper\Result\Relation\DynamicAttribute::parseAttribute()
	 */
	protected function parseMetadata(AnnotationsBag $annotations) {
		//obtain program source
		$this->program = new DynamicSQLProgram($annotations->get('Eval')->getValue());
	}
	
	public function evaluate($row, $mapper) {
		//evaluate condition
		if ($this->checkCondition($row, $mapper->getConfig()) === false) {
			return null;
		}
		
		$args = $this->evaluateArgs($row);
		return $this->program->executeWith($this->buildEnvironment($mapper->getConfig()), $args);
	}
}
?>