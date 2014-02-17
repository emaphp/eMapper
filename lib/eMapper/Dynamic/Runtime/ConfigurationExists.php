<?php
namespace eMapper\Dynamic\Runtime;

use eMacros\Applicable;
use eMacros\Scope;
use eMacros\GenericList;
use eMapper\Dynamic\Environment\ConfigurableEnvironment;

class ConfigurationExists implements Applicable {
	/**
	 * Configuration option
	 * @var string
	 */
	public $option;
	
	public function __construct($option = null) {
		$this->option = $option;
	}
	
	public function apply(Scope $scope, GenericList $arguments) {
		//validate environment
		if (!($scope instanceof ConfigurableEnvironment)) {
			throw new \RuntimeException("ConfigurationExists: Scope is not a valir configurable environment");
		}
		
		//obtain option name
		if (is_null($this->option)) {
			if (count($arguments) == 0) {
				throw new \BadFunctionCallException("ConfigurationExists: No parameters found.");
			}
			
			$option = $arguments[0]->evaluate($scope);
		}
		else {
			$option = $this->option;
		}
		
		return array_key_exists($option, $scope->config);
	}
}
?>