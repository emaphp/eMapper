<?php
namespace eMapper\Result\Relation;

use eMapper\Dynamic\Provider\EnvironmentProvider;

class StatementCallback extends DynamicAttribute {
	/**
	 * Statement ID
	 * @var string
	 */
	public $statement;
	
	public function __construct($name, $attribute) {
		parent::__construct($name, $attribute);
		
		//obtain statement id
		$this->statement = $attribute->get('stmt');
	}
	
	public function evaluate($row, $parameterMap, $mapper) {
		//evaluate condition
		if (isset($this->condition)) {
			$environmentId = $mapper->config['environment.id'];
		
			if (!EnvironmentProvider::hasEnvironment($environmentId)) {
				EnvironmentProvider::buildEnvironment($environmentId, $mapper->config['environment.class']);
			}
		
			$cond = $this->condition->execute(EnvironmentProvider::getEnvironment($environmentId), ParameterWrapper::wrap($row, $parameterMap));
		
			if ((bool) $cond === false) {
				return null;
			}
		}
		
		//build argument list
		$args = $this->evaluateArgs($row, $parameterMap);
		array_unshift($args, $this->statement);
		
		//merge mapper configuration
		$this->mergeConfig($mapper->config);
		
		//invoke statement
		return call_user_func_array(array($mapper->merge($this->config), 'execute'), $args);
	}
}
?>