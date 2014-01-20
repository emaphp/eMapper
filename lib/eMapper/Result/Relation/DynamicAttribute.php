<?php
namespace eMapper\Result\Relation;

use eMapper\Result\Argument\PropertyReader;

class DynamicAttribute {
	const PROPERTY_REGEX = '/^[^\\\\]([\w]+)$/';
	
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
	
	public function __construct($attribute) {
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
					$this->args[] = new PropertyReader($matches[1]);
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
		
		if ($attribute->has('map-type')) {
			$this->config['map.type'] = $attribute->get('map-type');
		}
		
		if ($attribute->has('result-map')) {
			$this->config['map.result'] = $attribute->get('result-map');
		}
		
		if ($attribute->has('parameter-map')) {
			$this->config['map.parameter'] = $attribute->get('parameter-map');
		}
	}
	
	/**
	 * Evaluates all attribute arguments against current instance
	 * @param ParameterWrapper $row
	 * @return array
	 */
	protected function evaluateArgs($row) {
		$args = array();
		
		if ($this->useDefaultArgument) {
			$args = $row;
		}
		
		foreach ($this->args as $arg) {
			if ($arg instanceof PropertyReader) {
				$args[] = $row[$arg->property];
			}
			else {
				$args[] = $arg;
			}
		}
		
		return $args;
	}
	
	/**
	 * Merges current configuration with mapper configuration
	 * @param array $config
	 */
	protected function mergeConfig($config) {
		$this->config['depth.current'] = $config['depth.current'] + 1;
	}
	
	public abstract function evaluate($row, $mapper);
}

?>