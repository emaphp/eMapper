<?php
namespace eMapper\Reflection\Profile\Dynamic;

use eMapper\Reflection\Parameter\ParameterWrapper;
use eMapper\Reflection\Profile\PropertyProfile;
use eMapper\Dynamic\Builder\EnvironmentBuilder;
use Omocha\Filter;
use Omocha\AnnotationBag;
use eMapper\Query\Attr;
use eMapper\Dynamic\Program\DynamicSQLProgram;
use eMapper\Mapper;

/**
 * The DynamicAttribute class defines the basic behaviour for entity dynamic attributes.
 * @author emaphp
 */
abstract class DynamicAttribute extends PropertyProfile {
	use EnvironmentBuilder;
	
	//Ex: name, userId:string
	const PARAMETER_PROPERTY_REGEX = '/([A-z|_]{1}[\w|\\\\]*)(:[A-z]{1}[\w|\\\\]*)?/';
	
	/**
	 * Attribute arguments
	 * @var array
	 */
	protected $args;
		
	/**
	 * Attribute configuration
	 * @var array
	 */
	protected $config;
	
	/**
	 * Indicates if current instance is passed as argument
	 * @var boolean
	 */
	protected $useDefaultArgument;
	
	/**
	 * Pre-condition macro
	 * @var \Closure
	 */
	protected $condition;
	
	public function __construct($name, AnnotationBag $annotations, \ReflectionProperty $reflectionProperty) {
		parent::__construct($name, $annotations, $reflectionProperty);

		$this->parseMetadata($annotations);
		$this->parseArguments($annotations);
		$this->parseConfig($annotations);
	}
	
	/**
	 * Parses attribute arguments
	 * @param AnnotationBag $attribute
	 */
	protected function parseArguments(AnnotationBag $annotations) {
		$this->args = [];
		
		//use object as argument
		if ($annotations->has('Self'))
			$this->useDefaultArgument = true;
		
		//parse additional arguments
		$parameters = $annotations->find('Parameter');
		
		foreach ($parameters as $param) {
			if ($param->hasArgument()) {
				$arg = $param->getArgument();
				
				if (preg_match(self::PARAMETER_PROPERTY_REGEX, $arg, $matches)) {
					if (array_key_exists(2, $matches))
						$this->args[] = Attr::__callstatic($matches[1], [substr($matches[2], 1)]);
					else
						$this->args[] = Attr::__callstatic($matches[1]);
				}
			}
			else
				$this->args[] = $param->getValue();
		}
		
		//use default argument
		if (empty($this->args))
			$this->useDefaultArgument = true;
	}
	
	/**
	 * Parses attribute configuration
	 * @param AnnotationBag $annotations
	 */
	protected function parseConfig(AnnotationBag $annotations) {
		$this->config = [];
		
		if (isset($this->type))
			$this->config['map.type'] = $this->type;
		
		if ($annotations->has('ResultMap'))
			$this->config['map.result'] = $annotations->get('ResultMap')->getValue();
		
		if ($annotations->has('ParameterMap'))
			$this->config['map.parameter'] = $annotations->get('ParameterMap')->getValue();
		
		if ($annotations->has('If')) {
			$cond = $annotations->get('If')->getValue();
			
			if (empty($cond))
				throw new \RuntimeException(sprintf("No condition defined for @If annotation in %s property", $this->name));
				
			$cond = new DynamicSQLProgram($cond);
			
			$this->condition = function ($row, $config) use ($cond) {
				return (bool) $cond->executeWith($this->buildEnvironment($config), [$row]);
			};
		}
		elseif ($annotations->has('IfNot')) {
			$cond = $annotations->get('IfNot')->getValue();
				
			if (empty($cond))
				throw new \RuntimeException(sprintf("No condition defined for @IfNot annotation in %s property", $this->name));
			
			$cond = new DynamicSQLProgram($cond);
			
			$this->condition = function ($row, $config) use ($cond) {
				return !(bool) $cond->executeWith($this->buildEnvironment($config), [$row]);
			};
		}
		elseif ($annotations->has('IfNotNull')) {
			$attr = $annotations->get('IfNotNull')->getArgument();
			
			if (empty($attr))
				throw new \RuntimeException(sprintf("No attribute name defined for @IfNotNull annotation in %s property", $this->name));
			
			$this->condition = function($row, $config) use ($attr) {
				$wrapper = ParameterWrapper::wrapValue($row);
				return !($wrapper->offsetExists($attr) && $wrapper->offsetGet($attr) === null);
			};
		}
		
		//get additional options [Option(KEY) VALUE]
		$options = $annotations->find('Option', Filter::HAS_ARGUMENT);
		
		foreach ($options as $option)
			$this->config[$option->getArgument()] = $option->getValue();
	}
	
	/**
	 * Evaluates all attribute arguments against current instance
	 * @param mixed $row
	 * @return array
	 */
	protected function evaluateArgs($row) {
		$args = [];
		$wrapper = ParameterWrapper::wrapValue($row);
		
		if ($this->useDefaultArgument)
			$args[] = $row;
		
		foreach ($this->args as $arg) {
			if ($arg instanceof Attr) {
				if (!$wrapper->offsetExists($arg->getName()))
					throw new \InvalidArgumentException(sprintf("Property '%s' was not found whe evaluating arguments for %s attribute", $arg->getName(), $this->name));
				
				$args[] = $wrapper->offsetGet($arg->getName());
			}
			else
				$args[] = $arg;
		}
		
		return $args;
	}
		
	/**
	 * Checks current condition with the given values
	 * @param mixed $row
	 * @param array $config
	 * @return boolean
	 */
	protected function checkCondition($row, $config) {
		if (isset($this->condition))
			return call_user_func($this->condition, $row, $config);
		
		return true;
	}

	/**
	 * Parse additional metadata in a property
	 * @param AnnotationBag $annotations
	 */
	protected abstract function parseMetadata(AnnotationBag $annotations);
	
	/**
	 * Evaluates the current attribute
	 * @param mixed $row
	 * @param Mapper $mapper
	 */
	public abstract function evaluate($row, Mapper $mapper);
}

?>