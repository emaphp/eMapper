<?php
namespace eMapper\Result\Relation;

class QueryCallback extends DynamicAttribute {
	/**
	 * Raw query
	 * @var string
	 */
	public $query;
	
	protected function parseAttribute($attribute) {
		//obtain query
		$this->query = $attribute->get('map.query');
	}
	
	public function evaluate($row, $parameterMap, $mapper) {
		//evaluate condition
		if ($this->checkCondition($row, $parameterMap, $mapper->config) === false) {
			return null;
		}
		
		//build argument list
		$args = $this->evaluateArgs($row, $parameterMap);
		array_unshift($args, $this->query);

		//apply configuration
		$this->applyConfig($mapper->config);
		
		//invoke statement
		return call_user_func_array([$mapper->merge($this->config), 'query'], $args);
	}
}
?>