<?php
namespace eMapper\Result\Relation;

class QueryCallback extends DynamicAttribute {
	/**
	 * Raw query
	 * @var string
	 */
	public $query;
	
	public function __construct($attribute) {
		parent::__construct($attribute);
		
		//obtain query
		$this->query = $attribute->get('query');
	}
	
	public function evaluate($row, $mapper) {
		//build argument list
		$args = $this->evaluateArgs($row);
		array_unshift($args, $this->query);

		//merge mapper configuration
		$this->mergeConfig($mapper->config);
		
		//invoke statement
		return call_user_func_array(array($mapper->merge($this->config), 'query'), $args);
	}
}
?>