<?php
namespace eMapper\Result\Relation;

use eMapper\Annotations\AnnotationsBag;

/**
 * The QueryCallback class implements the logic for evaluating queries againts
 * a list of arguments.
 * @author emaphp
 */
class QueryCallback extends DynamicAttribute {
	/**
	 * Raw query
	 * @var string
	 */
	protected $query;
	
	public function getQuery() {
		return $this->query;
	}
	
	protected function parseMetadata(AnnotationsBag $annotations) {
		//obtain query
		$this->query = $annotations->get('Query')->getValue();
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