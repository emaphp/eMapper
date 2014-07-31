<?php
namespace eMapper\Dynamic\Runtime;

use eMacros\Applicable;
use eMacros\Scope;
use eMacros\GenericList;

/**
 * The ConfigurationExists class is a macro that determines if a given configuration
 * key is set in a given environment.
 * @author emaphp
 */
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
		
		return $scope->hasOption($option);
	}
}
?>