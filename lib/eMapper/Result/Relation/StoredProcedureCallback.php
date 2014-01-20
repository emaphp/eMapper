<?php
namespace eMapper\Result\Relation;

class StoredProcedureCallback extends DynamicAttribute {
	/**
	 * Stored procedure name
	 * @var string
	 */
	public $procedure;
	
	public function __construct($attribute) {
		parent::__construct($attribute);
		
		//obtain procedure name
		$this->procedure = $attribute->get('procedure');
	}
	
	public function evaluate($row, $mapper) {
		//build argument list
		$args = $this->evaluateArgs($row);
		array_unshift($args, $this->procedure);
		
		//merge mapper configuration
		$this->mergeConfig($mapper->config);
		
		//call stored procedure
		return call_user_func_array(array($mapper->merge($this->config), '_call'), $args);
	}
}
?>