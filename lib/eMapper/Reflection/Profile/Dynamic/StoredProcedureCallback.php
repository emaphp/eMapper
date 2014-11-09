<?php
namespace eMapper\Reflection\Profile\Dynamic;

use eMapper\Reflection\Parameter\ParameterWrapper;
use eMapper\Reflection\Profiler;
use Omocha\AnnotationBag;
use eMapper\Query\Attr;
use eMapper\Procedure\StoredProcedure;
use eMapper\Mapper;

class StoredProcedureCallback extends DynamicAttribute {
	/**
	 * Stored procedure instance
	 * @var StoredProcedure
	 */
	protected $procedure;
	
	/**
	 * Stored procedure name
	 * @var string
	 */
	protected $procedureName;
	
	/**
	 * Determines if the procedure is evaluated as a table (PostgreSQL)
	 * @var boolean
	 */
	protected $asTable = false;
	
	/**
	 * Determines if the database prefix must be included in the procedure name
	 * @var boolean
	 */
	protected $usePrefix = true;
	
	/**
	 * Determines if the procedure name must be escaped
	 * @var boolean
	 */
	protected $escapeName = false;

	public function __construct($name, AnnotationBag $annotations, \ReflectionProperty $reflectionProperty) {
		parent::__construct($name, $annotations, $reflectionProperty);
	
		$this->parseMetadata($annotations);
		$this->parseArguments($annotations);
		$this->parseConfig($annotations);
		
		
	}
	
	protected function parseMetadata(AnnotationBag $annotations) {
		//obtain procedure name
		$this->procedureName = $annotations->get('Procedure')->getValue();
		
		if ($annotations->has('AsTable')) {
			$this->asTable = (bool) $annotations->get('AsTable')->getValue();
		}
		
		if ($annotations->has('UsePrefix')) {
			$this->usePrefix = (bool) $annotations->get('UsePrefix')->getValue();
		}
		
		if ($annotations->has('Escape')) {
			$this->escapeName = (bool) $annotations->get('Escape')->getValue();
		}
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
				if (!$wrapper->offsetExists($name))
					throw new \InvalidArgumentException(sprintf("Property '%s' was not found whe evaluating arguments for %s attribute", $arg->getName(), $this->name));
				
				//get attribute value and type
				$value = $wrapper->offsetGet($name);
				$type = $arg->getType();
				
				if (is_null($type)) {
					$type = $profile->getProperty($name)->getType();
					
					if (!isset($type))
						$type = strtolower(gettype($value));
				}
				
				$args[] = $value;
				$proc_types[] = $type;
			}
			else
				$args[] = $arg;
		}
	
		return $args;
	}
	
	protected function buildProcedure(Mapper $mapper) {
		//create procedure instance
		$this->procedure = $mapper->merge($this->config)->newProcedureCall($this->procedureName);
		
		//configure procedure
		$this->procedure->as_table($this->asTable);
		$this->procedure->use_prefix($this->usePrefix);
		$this->procedure->escape($this->escapeName);
	}
	
	public function evaluate($row, Mapper $mapper) {
		$this->buildProcedure($mapper);
		
		//evaluate condition
		if ($this->checkCondition($row, $mapper->getConfig()) === false)
			return null;
		
		//build argument type list
		$types = [];
		$args = $this->evaluateArgs($row, $types);
		$this->procedure->types($types);

		//call procedure
		return $this->procedure->callWith($args);
	}
}
?>