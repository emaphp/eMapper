<?php
namespace eMapper\Result\Relation;

use eMapper\Reflection\Parameter\ParameterWrapper;
use eMapper\Reflection\Profile\PropertyProfile;
use eMacros\Program\Program;
use eMacros\Program\SimpleProgram;
use eMapper\Dynamic\Provider\EnvironmentProvider;
use eMacros\Environment\Environment;
use eMapper\Dynamic\Builder\EnvironmentBuilder;
use Minime\Annotations\AnnotationsBag;
use eMapper\Query\Attr;

abstract class DynamicAttribute extends PropertyProfile {
	use EnvironmentBuilder;
	
	//Ex: Parameter(name), Parameter(userId:string)
	const PARAMETER_PROPERTY_REGEX = '/Parameter(?:\(([A-z|_]{1}[\w|\\\\]*)(:[A-z]{1}[\w|\\\\]*)?\))/';
	
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
	
	/**
	 * Determines if the condition value must be reversed
	 * @var boolean
	 */
	public $reverseCondition = false;
	
	public function __construct($name, AnnotationsBag $annotations, \ReflectionProperty $reflectionProperty) {
		parent::__construct($name, $annotations, $reflectionProperty);

		$this->parseMetadata($annotations);
		$this->parseArguments($annotations);
		$this->parseConfig($annotations);
	}
	
	/**
	 * Parses attribute arguments
	 * @param AnnotationBag $attribute
	 */
	protected function parseArguments(AnnotationsBag $annotations) {
		$this->args = [];
		
		//use object as argument
		if ($annotations->has('Self')) {
			$this->useDefaultArgument = true;
		}
		
		//parse additional arguments
		$parameters = $annotations->grep('^Parameter')->export();
		
		foreach ($parameters as $name => $param) {
			//check if the parameter defines a property
			if (preg_match(self::PARAMETER_PROPERTY_REGEX, $name, $matches)) {
				//check if a type has been added
				if (array_key_exists(2, $matches)) {
					$this->args[] = Attr::__callstatic($matches[1], [substr($matches[2], 1)]);
				}
				else {
					$this->args[] = Attr::__callstatic($matches[1]);
				}
			}
			else {
				$this->args[] = $param;
			}
		}
		
		//use default argument
		if (empty($this->args)) {
			$this->useDefaultArgument = true;
		}
	}
	
	/**
	 * Parses attribute configuration
	 * @param AnnotationBag $annotations
	 */
	protected function parseConfig(AnnotationsBag $annotations) {
		$this->config = [];
		
		if (isset($this->type)) {
			$this->config['map.type'] = $this->type;
		}
		
		if ($annotations->has('ResultMap')) {
			$this->config['map.result'] = $annotations->get('ResultMap');
		}
		
		if ($annotations->has('ParameterMap')) {
			$this->config['map.parameter'] = $annotations->get('ParameterMap');
		}
		
		if ($annotations->has('If')) {
			$this->condition = new SimpleProgram($annotations->get('If'));
		}
		elseif ($annotations->has('IfNot')) {
			$this->condition = new SimpleProgram($annotations->get('IfNot'));
			$this->reverseCondition = true;
		}
		
		//get additional options [Option(KEY) VALUE]
		$options = $annotations->grep('^Option\([\w|\.]+\)')->export();
		
		foreach ($options as $name => $value) {
			if (is_string($option) && preg_match('/Option\(([\w|\.]+)\)/', $name, $matches)) {
				$this->config[$matches[1]] = $value;
			}
		}
	}
	
	/**
	 * Evaluates all attribute arguments against current instance
	 * @param mixed $row
	 * @return array
	 */
	protected function evaluateArgs($row, $parameterMap) {
		$args = [];
		$wrapper = ParameterWrapper::wrap($row, $parameterMap);
		
		if ($this->useDefaultArgument) {
			$args[] = $row;
		}
		
		foreach ($this->args as $arg) {
			$args[] = $arg instanceof Attr ? $wrapper[$arg->getName()] : $arg;
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
			$condition = (bool) $this->condition->execute($this->buildEnvironment($config), ParameterWrapper::wrap($row, $parameterMap));
			
			if ($this->reverseCondition) {
				return !$condition;
			}
			
			return $condition;
		}
		
		return true;
	}
	
	/**
	 * Updates configuration values before evaluation
	 * @param array $config
	 */
	protected function updateConfig($config) {
		$this->config['depth.current'] = $config['depth.current'] + 1;
	}
	
	protected abstract function parseMetadata(AnnotationsBag $annotations);
	public abstract function evaluate($row, $parameterMap, $mapper);
}

?>