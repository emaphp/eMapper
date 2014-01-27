<?php
namespace eMapper\Dynamic\Runtime;

use eMacros\Applicable;
use eMacros\Scope;
use eMacros\GenericList;
use eMapper\Reflection\Parameter\ParameterWrapper;

class PropertyExists implements Applicable {
	/**
	 * Property name
	 * @var string
	 */
	public $property;
	
	public function __construct($property = null) {
		$this->property = $property;
	}
	
	public function apply(Scope $scope, GenericList $arguments) {
		//get property and value
		if (is_null($this->property)) {
			if (empty($arguments)) {
				throw new \BadFunctionCallException("PropertyExists: No parameters found.");
			}
				
			$property = $arguments[0]->evaluate($scope);
				
			if (count($arguments) == 1) {
				if (!array_key_exists(0, $scope->arguments)) {
					throw new \BadFunctionCallException("PropertyExists: Expected value of type array/object as second parameter but none found.");
				}
		
				$value = $scope->arguments[0];
			}
			else {
				$value = $arguments[1]->evaluate($scope);
			}
		}
		else {
			$property = $this->property;
				
			if (count($arguments) == 0) {
				if (!array_key_exists(0, $scope->arguments)) {
					throw new \BadFunctionCallException("PropertyExists: Expected value of type array/object as first parameter but none found.");
				}
		
				$value = $scope->arguments[0];
			}
			else {
				$value = $arguments[0]->evaluate($scope);
			}
		}
		
		//check value type
		if (!is_array($value) && !is_object($value)) {
			throw new \InvalidArgumentException(sprintf("PropertyExists: Expected value of type array/object but %s found instead", gettype($value)));
		}
		
		//generate wrapper instance if necessary
		if ($value instanceof ParameterWrapper) {
			return $value->offsetExists($property);
		}
		else {
			$wrapper = ParameterWrapper::wrap($value);
			return $wrapper->offsetExists($property);
		}
	}
}
?>