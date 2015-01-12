<?php
namespace eMapper\ORM\Dynamic;

use eMapper\Reflection\Profiler;
use eMapper\Reflection\Argument\ArgumentWrapper;
use eMapper\Query\Attr;
use Omocha\AnnotationBag;
use eMapper\Mapper;
use eMapper\Engine\PostgreSQL\Procedure\PostgreSQLStoredProcedure;

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
	
	/**
	 * Value indicated through @UsePrefix
	 * @var boolean
	 */
	protected $usePrefix;
	
	/**
	 * Value indicated through @ArgTypes
	 * @var boolean
	 */
	protected $argumentTypes;
	
	/**
	 * Value indicated through @ReturnSet (only PostgreSQL)
	 * @var boolean
	 */
	protected $returnSet;
	
	/**
	 * Value indicated through @EscapeName (only PostgreSQL)
	 * @var boolean
	 */
	protected $escapeName;
	
	protected function parseMetadata(AnnotationBag $propertyAnnotations) {
		//obtain procedure name
		$this->procedureName = $propertyAnnotations->get('Procedure')->getValue();
	}
	
	protected function parseConfig(AnnotationBag $propertyAnnotations) {
		parent::parseConfig($propertyAnnotations);
		
		if ($propertyAnnotations->has('UsePrefix'))
			$this->usePrefix = (bool) $propertyAnnotations->get('UsePrefix')->getValue();
		
		if ($propertyAnnotations->has('ArgTypes'))
			$this->argumentTypes = explode(',', $propertyAnnotations->get('ArgTypes')->getArgument());
		
		//PostgreSQL options
		if ($propertyAnnotations->has('ReturnSet'))
			$this->returnSet = (bool) $propertyAnnotations->get('ReturnSet')->getValue();
		
		if ($propertyAnnotations->has('EscapeName'))
			$this->escapeName = (bool) $propertyAnnotations->get('EscapeName')->getValue();
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
		$this->procedure = $mapper->newProcedure($this->procedureName);
		
		if (isset($this->usePrefix))
			$this->procedure->usePrefix($this->usePrefix);
		
		if (!empty($this->argumentTypes))
			$this->procedure->argTypes($this->argumentTypes);
		
		//PostgreSQL options
		if ($this->procedure instanceof PostgreSQLStoredProcedure) {
			if (isset($this->returnSet))
				$this->procedure->returnSet($this->returnSet);
			
			if (isset($this->escapeName))
				$this->procedure->escapeName($this->escapeName);
		}
		
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