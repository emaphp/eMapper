<?php
namespace eMapper\Result\Relation;

class QueryCallback extends DynamicAttribute {
	/**
	 * Raw query
	 * @var string
	 */
	public $query;
	
	public function __construct($name, $attribute) {
		parent::__construct($name, $attribute);
		
		//obtain query
		$this->query = $attribute->get('query');
	}
	
	public function evaluate($row, $parameterMap, $mapper) {
		//build argument list
		$args = $this->evaluateArgs($row, $parameterMap);
		array_unshift($args, $this->query);

		//merge mapper configuration
		$this->mergeConfig($mapper->config);
		
		//invoke statement
		return call_user_func_array(array($mapper->merge($this->config), 'query'), $args);
	}
}
?>