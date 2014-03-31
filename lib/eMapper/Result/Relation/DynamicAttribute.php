<?php
namespace eMapper\Result\Relation;

use eMapper\Result\Argument\PropertyReader;
use eMapper\Reflection\Parameter\ParameterWrapper;
use eMapper\Reflection\Profile\PropertyProfile;
use eMacros\Program\Program;
use eMacros\Program\SimpleProgram;
use eMapper\Dynamic\Provider\EnvironmentProvider;
use eMacros\Environment\Environment;
use eMapper\Dynamic\Builder\EnvironmentBuilder;

abstract class DynamicAttribute extends PropertyProfile {
	use EnvironmentBuilder;
	
	const PROPERTY_REGEX = '/^[^\\\\]([\w]+)(?::([A-z]{1}[\w|\\\\]*))?$/';
	
	/**
	 * Attribute arguments
	 * @var array
	 */
	public $args;
		
	/**
	 * Attribute configuration
	 * @var array
	 */
	public $config;
	
	/**
	 * Indicates if current instance is passed as argument
	 * @var boolean
	 */
	public $useDefaultArgument;
	
	/**
	 * Pre-condition macro
	 * @var Program
	 */
	public $condition;
	
	public function __construct($name, $attribute, \ReflectionProperty $reflectionProperty) {
		parent::__construct($name, $attribute, $reflectionProperty);

		$this->parseAttribute($attribute);
		$this->parseArguments($attribute);
		$this->parseConfig($attribute);
	}
	
	/**
	 * Parse attribute arguments
	 * @param AnnotationBag $attribute
	 */
	protected function parseArguments($attribute) {
		$this->args = array();
		
		if ($attribute->has('map.self-arg')) {
			$this->useDefaultArgument = true;
		}
		
		$arg_list = $attribute->getAsArray('map.arg');
				
		foreach ($arg_list as $arg) {
			if (is_string($arg)) {
				//check if argument is a reference to a instance property
				if (preg_match(self::PROPERTY_REGEX, $arg, $matches)) {
					if (isset($matches[2])) {
						$this->args[] = new PropertyReader($matches[1], $matches[2]);
					}
					else {
						$this->args[] = new PropertyReader($matches[1]);
					}
				}
				else {
					$this->args[] = str_replace('\\#', '#', $arg);
				}
			}
			else {
				$this->args[] = $arg;
			}
		}
		
		//use default argument
		if (empty($this->args)) {
			$this->useDefaultArgument = true;
		}
	}
	
	/**
	 * Parse attribute configuration
	 * @param AnnotationBag $attribute
	 */
	protected function parseConfig($attribute) {
		$this->config = array();
		
		if (isset($this->type)) {
			$this->config['map.type'] = $this->type;
		}
		
		if ($attribute->has('map.result-map')) {
			$this->config['map.result'] = $attribute->get('map.result-map');
		}
		
		if ($attribute->has('map.parameter-map')) {
			$this->config['map.parameter'] = $attribute->get('map.parameter-map');
		}
		
		if ($attribute->has('map.cond')) {
			$this->condition = new SimpleProgram($attribute->get('map.cond'));
		}
		
		//get additional options
		$options = $attribute->useNamespace('map.option');
		
		if ($options->count() != 0) {
			$this->config = array_merge($this->config, $options->export());
		}
	}
	
	/**
	 * Evaluates all attribute arguments against current instance
	 * @param mixed $row
	 * @return array
	 */
	protected function evaluateArgs($row, $parameterMap) {
		$args = array();
		$wrapper = ParameterWrapper::wrap($row, $parameterMap);
		
		if ($this->useDefaultArgument) {
			$args[] = $row;
		}
		
		foreach ($this->args as $arg) {
			if ($arg instanceof PropertyReader) {
				$args[] = $wrapper[$arg->property];
			}
			else {
				$args[] = $arg;
			}
		}
		
		return $args;
	}
		
	/**
	 * Checks current condition with the given values
	 * @param mixed $row
	 * @param string $parameterMap
	 * @param array $config
	 * @return boolean
	 */
	protected function checkCondition($row, $parameterMap, $config) {
		if (isset($this->condition)) {
			return (bool) $this->condition->execute($this->buildEnvironment($config), ParameterWrapper::wrap($row, $parameterMap));
		}
		
		return true;
	}
	
	/**
	 * Applies configuration values for attribute evaluation
	 * @param array $config
	 */
	protected function applyConfig($config) {
		$this->config['depth.current'] = $config['depth.current'] + 1;
	}
	
	protected abstract function parseAttribute($attribute);
	public abstract function evaluate($row, $parameterMap, $mapper);
}

?>