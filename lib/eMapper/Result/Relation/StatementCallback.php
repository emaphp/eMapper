<?php
namespace eMapper\Result\Relation;

class StatementCallback extends DynamicAttribute {
	/**
	 * Statement ID
	 * @var string
	 */
	public $statement;
	
	public function __construct($attribute) {
		parent::__construct($attribute);
		
		//obtain statement id
		$this->statement = $attribute->get('stmt');
	}
	
	public function evaluate($row, $mapper) {
		//build argument list
		$args = $this->evaluateArgs($row);
		array_unshift($args, $this->statement);
		
		//merge mapper configuration
		$this->mergeConfig($mapper->config);
		
		//invoke statement
		return call_user_func_array(array($mapper->merge($this->config), 'execute'), $args);
	}
}
?>