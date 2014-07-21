<?php
namespace eMapper\Result\Relation;

use eMapper\Annotations\AnnotationsBag;

class StatementCallback extends DynamicAttribute {
	/**
	 * Statement ID
	 * @var string
	 */
	public $statement;
	
	protected function parseMetadata(AnnotationsBag $attribute) {
		//obtain statement id
		$this->statement = $attribute->get('StatementId')->getValue();
	}
	
	public function evaluate($row, $parameterMap, $mapper) {
		//evaluate condition
		if ($this->checkCondition($row, $parameterMap, $mapper->config) === false) {
			return null;
		}
		
		//build argument list
		$args = $this->evaluateArgs($row, $parameterMap);
		array_unshift($args, $this->statement);
		
		//update configuration
		$this->updateConfig($mapper->config);
		
		//invoke statement
		return call_user_func_array([$mapper->merge($this->config), 'execute'], $args);
	}
}
?>