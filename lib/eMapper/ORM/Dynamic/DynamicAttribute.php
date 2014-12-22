<?php
namespace eMapper\ORM\Dynamic;

use eMapper\Reflection\ClassProperty;
use eMapper\Reflection\Argument\ArgumentWrapper;
use eMapper\Query\Attr;
use eMapper\Dynamic\Program\DynamicSQLProgram;
use eMapper\Dynamic\Builder\EnvironmentBuilder;
use Omocha\Filter;
use Omocha\AnnotationBag;
use eMapper\Mapper;

/**
 * The DynamicAttribute class defines the basic behaviour for entity dynamic attributes.
 * @author emaphp
 */
abstract class DynamicAttribute extends ClassProperty {
	use EnvironmentBuilder;
	
	/**
	 * Attribute arguments
	 * @var array
	 */
	protected $args;
	
	/**
	 * Indicates if mapped instance is used as argument
	 * @var boolean
	 */
	protected $useDefaultArgument;
	
	/**
	 * Additional configuration
	 * @var array
	 */
	protected $config;
	
	/**
	 * Condition program
	 * @var \eMapper\Dynamic\Program\DynamicSQLProgram
	 */
	protected $program;
	
	/**
	 * Condition program wrapper
	 * @var \Closure
	 */
	protected $condition;
	
	/**
	 * Determines if this attribute is cacheable
	 * @var boolean
	 */
	protected $cacheable = false;
	
	public function __construct($propertyName, \ReflectionProperty $reflectionProperty, AnnotationBag $propertyAnnotations) {
		parent::__construct($propertyName, $reflectionProperty, $propertyAnnotations);
		
		//parse annotations
		$this->parseMetadata($propertyAnnotations);
		$this->parseArguments($propertyAnnotations);
		$this->parseConfig($propertyAnnotations);
	}
	
	/**
	 * Parses additional configuration values
	 * @param \Omocha\AnnotationBag $propertyAnnotations
	 */
	protected abstract function parseMetadata(AnnotationBag $propertyAnnotations);
	
	/**
	 * Evaluates attribute
	 * @param array | object $row
	 * @param \eMapper\Mapper $mapper
	 */
	abstract function evaluate($row, Mapper $mapper);
	
	/**
	 * Parses attribute argument list
	 * @param \Omocha\AnnotationBag $propertyAnnotations
	 */
	protected function parseArguments(AnnotationBag $propertyAnnotations) {
		$this->args = [];
		
		//parse additional arguments
		$parameters = $propertyAnnotations->find('Param');
		
		foreach ($parameters as $param) {
			if ($param->hasArgument()) {
				$arg = $param->getArgument();
				
				if (strtolower($arg) == 'self')
					$this->useDefaultArgument = true;
				else
					$this->args[] = Attr::__callstatic($arg);
			}
			else
				$this->args[] = $param->getValue();
		}
		
		//use default argument if no arguments are specified
		if (empty($this->args))
			$this->useDefaultArgument = true;
	}
	
	/**
	 * Parses attribute configuration
	 * @param \Omocha\AnnotationBag $propertyAnnotations
	 */
	protected function parseConfig(AnnotationBag $propertyAnnotations) {
		$this->config = [];
		
		//mapping type expression
		if (isset($this->type))
			$this->config['map.type'] = $this->type;
		
		//result map
		if ($propertyAnnotations->has('ResultMap'))
			$this->config['map.result'] = $propertyAnnotations->get('ResultMap')->getValue();
		
		//cache
		if ($propertyAnnotations->has('Cache')) {
			$this->config['cache.ttl'] = intval($propertyAnnotations->get('Cache')->getArgument());
			$this->config['cache.key'] = $propertyAnnotations->get('Cache')->getValue();
		}
		
		//custom options
		$options = $propertyAnnotations->find('Option', Filter::HAS_ARGUMENT);
		
		foreach ($options as $option)
			$this->config[$option->getArgument()] = $option->getValue();
		
		//cacheable
		if ($propertyAnnotations->has('Cacheable')) {
			//check type before assuming is cacheable
			$this->cacheable = !empty($this->type) && !preg_match('/^[arr|array|obj|object]:/', $this->type);
		}
		
		//get evaluation condition
		if ($propertyAnnotations->has('If')) {
			$this->program = new DynamicSQLProgram($propertyAnnotations->get('If')->getValue());
			$this->condition = function ($row, $config) {
				return (bool) $this->program->execute($this->buildEnvironment($config), $row);
			};
		}
		elseif ($propertyAnnotations->has('IfNot')) {
			$this->program = new DynamicSQLProgram($propertyAnnotations->get('IfNot')->getValue());
			$this->condition = function ($row, $config) {
				return !(bool) $this->program->execute($this->buildEnvironment($config), $row);
			};
		}
		elseif ($propertyAnnotations->has('IfNotNull')) {
			$attribute = $propertyAnnotations->get('IfNotNull')->getArgument();
			$this->condition = function($row, $_) use ($attribute) {
				$argument = ArgumentWrapper::wrap($row);
				return !($argument->offsetExists($attribute) && $argument->offsetGet($attribute) === null);
			};
		}
	}
	
	/**
	 * Builds the list of arguments for the current attribute
	 * @param array | object $row
	 * @return array
	 */
	protected function evaluateArguments($row) {
		$args = [];
		$argument = ArgumentWrapper::wrap($row);
		
		if ($this->useDefaultArgument)
			$args[] = $row;
			
		foreach ($this->args as $arg) {
			if ($arg instanceof Attr)
				$args[] = $argument->offsetExists($arg->getName()) ? $argument->offsetGet($arg->getName()) : null;
			else
				$args[] = $arg;
		}
		
		return $args;
	}
	
	/**
	 * Evaluates attribute condition
	 * @param array | object $row
	 * @param array $config
	 * @return bool
	 */
	protected function evaluateCondition($row, $config) {
		if (isset($this->condition))
			return call_user_func($this->condition, $row, $config);
		return true;
	}
	
	/**
	 * Finds whether this attribute is cacheable
	 * @return boolean
	 */
	public function isCacheable() {
		return $this->cacheable;
	}
}