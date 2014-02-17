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
	 * Class which declares this attribute
	 * @var string
	 */
	public $classname;
	
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
	
	public function __construct($classname, $name, $attribute) {
		parent::__construct($name, $attribute);
		$this->classname = $classname;
		$this->parseArguments($attribute);
		$this->parseConfig($attribute);
	}
	
	/**
	 * Parse attribute arguments
	 * @param AnnotationBag $attribute
	 */
	protected function parseArguments($attribute) {
		$this->args = array();
		
		if ($attribute->has('arg-self')) {
			$this->useDefaultArgument = true;
		}
		
		$arg_list = $attribute->getAsArray('arg');
				
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
		
		if ($attribute->has('result-map')) {
			$this->config['map.result'] = $attribute->get('result-map');
		}
		
		if ($attribute->has('parameter-map')) {
			$this->config['map.parameter'] = $attribute->get('parameter-map');
		}
		
		if ($attribute->has('cond')) {
			$this->condition = new SimpleProgram($attribute->get('cond'));
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
	 * Merges current configuration with mapper configuration
	 * @param array $config
	 */
	protected function mergeConfig($config) {
		$this->config['depth.current'] = $config['depth.current'] + 1;
	}
	
	public abstract function evaluate($row, $parameterMap, $mapper);
}

?>