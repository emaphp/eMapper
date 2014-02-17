<?php
namespace eMapper\Dynamic\Runtime;

use eMacros\Applicable;
use eMacros\Scope;
use eMacros\GenericList;
use eMapper\Dynamic\Environment\ConfigurableEnvironment;

class ConfigurationGet implements Applicable {
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
			throw new \RuntimeException("ConfigurationGet: Scope is not a valir configurable environment");
		}
		
		//obtain option name
		if (is_null($this->option)) {
			if (count($arguments) == 0) {
				throw new \BadFunctionCallException("ConfigurationGet: No parameters found.");
			}
				
			$option = $arguments[0]->evaluate($scope);
		}
		else {
			$option = $this->option;
		}

		//check option
		if (array_key_exists($option, $scope->config)) {
			throw new \InvalidArgumentException(sprintf("ConfigurationGet: Configuration key '%s' not found", $option));
		}
		
		return $scope->config[$option];
	}
}
?>