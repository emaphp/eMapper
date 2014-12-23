<?php
namespace eMapper\ORM\Dynamic;

use eMapper\Reflection\Profiler;
use eMapper\Reflection\Argument\ArgumentWrapper;
use eMapper\Query\Attr;
use Omocha\AnnotationBag;
use eMapper\Mapper;

/**
 * The Procedure class invokes a procedure with the arguments specified in a given property.
 * @author emaphp
 */
class Procedure extends DynamicAttribute {
	/**
	 * Invoked procedure
	 * @var \eMapper\Procedure\StoredProcedure
	 */
	protected $procedure;
	
	/**
	 * Procedure name
	 * @var string
	 */
	protected $procedureName;
		
	protected function parseMetadata(AnnotationBag $propertyAnnotations) {
		//obtain procedure name
		$this->procedureName = $propertyAnnotations->get('Procedure')->getValue();
	}
	
	protected function parseConfig(AnnotationBag $propertyAnnotations) {
		parent::parseConfig($propertyAnnotations);
		
		if ($propertyAnnotations->has('ReturnSet'))
			$this->config['proc.returnSet'] = (bool) $propertyAnnotations->get('ReturnSet')->getValue();
		
		if ($propertyAnnotations->has('UsePrefix'))
			$this->config['proc.usePrefix'] = (bool) $propertyAnnotations->get('UsePrefix')->getValue();
		
		if ($propertyAnnotations->has('EscapeName'))
			$this->config['proc.escapeName'] = (bool) $propertyAnnotations->get('EscapeName')->getValue();
		
		if ($propertyAnnotations->has('Types')) {
			$this->config['proc.types'] = explode(',', $propertyAnnotations->get('Types')->getArgument());
		}
	}
	
	protected function evaluateArguments($row) {
		$args = [];
		$argument = ArgumentWrapper::wrap($row);
		
		foreach ($this->args as $arg) {
			if ($arg instanceof Attr) {
				$attribute = $arg->getName();
				$args[] = $argument->offsetExists($attribute) ? $argument->offsetGet($attribute) : null;
			}
			else
				$args[] = $arg;
		}
		
		return $args;
	}
	
	/**
	 * Builds a StoredProcedure instance with the required configuration
	 * @param \eMapper\Mapper $mapper
	 */
	protected function buildProcedure(Mapper $mapper) {
		//create procedure instance
		$this->procedure = $mapper->newProcedureCall($this->procedureName);
		
		//configure procedure
		$this->procedure->merge($this->config);
	}
	
	public function evaluate($row, Mapper $mapper) {
		if ($this->evaluateCondition($row, $mapper->getConfig()) === false)
			return null;
		
		$this->buildProcedure($mapper);
		$args = $this->evaluateArguments($row);

		//call procedure
		return $this->procedure->callWith($args);
	}
}