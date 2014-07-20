<?php
namespace eMapper\Result\Relation;

use Minime\Annotations\AnnotationsBag;

class QueryCallback extends DynamicAttribute {
	/**
	 * Raw query
	 * @var string
	 */
	public $query;
	
	protected function parseMetadata(AnnotationsBag $annotations) {
		//obtain query
		$this->query = $annotations->get('Query');
	}
	
	public function evaluate($row, $parameterMap, $mapper) {
		//evaluate condition
		if ($this->checkCondition($row, $parameterMap, $mapper->config) === false) {
			return null;
		}
		
		//build argument list
		$args = $this->evaluateArgs($row, $parameterMap);
		array_unshift($args, $this->query);

		//update configuration
		$this->updateConfig($mapper->config);
		
		//invoke statement
		return call_user_func_array([$mapper->merge($this->config), 'query'], $args);
	}
}
?>