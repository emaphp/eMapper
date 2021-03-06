<?php
namespace eMapper\Dynamic\Runtime;

use eMacros\Applicable;
use eMacros\Scope;
use eMacros\GenericList;

/**
 * The ConfigurationGet class is a macro that obtains a configuration
 * value from a given environment.
 * @author emaphp
 */
class ConfigurationGet implements Applicable {
	/**
	 * Configuration option
	 * @var string
	 */
	protected $option;
	
	public function __construct($option = null) {
		$this->option = $option;
	}
	
	public function apply(Scope $scope, GenericList $arguments) {		
		//obtain option name
		if (is_null($this->option)) {
			if (count($arguments) == 0)
				throw new \BadFunctionCallException("ConfigurationGet: No parameters found.");
				
			$option = $arguments[0]->evaluate($scope);
		}
		else
			$option = $this->option;

		//check option
		if (!$scope->hasOption($option))
			return '';
		
		return $scope->getOption($option);
	}
}
?>