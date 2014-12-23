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
	protected $macro;
	
	protected function parseMetadata(AnnotationBag $propertyAnnotations) {
		$this->macro = new DynamicSQLProgram($propertyAnnotations->get('Eval')->getValue());
		$this->cacheable = true;
	}
	
	public function evaluate($row, Mapper $mapper) {
		if ($this->evaluateCondition($row, $mapper->getConfig()) === false)
			return null;
			
		$args = $this->evaluateArguments($row);
		return $this->macro->executeWith($this->buildEnvironment($mapper->getConfig()), $args);
	}
}