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
	protected $property;
	
	public function __construct($property = null) {
		$this->property = $property;
	}
	
	public function apply(Scope $scope, GenericList $arguments) {
		$useWrappedArgument = false;
		
		//get property and value
		if (is_null($this->property)) { // (#? 'propertyName' ...)
			if (count($arguments) == 0) {
				throw new \BadFunctionCallException("PropertyExists: No parameters found.");
			}
				
			$property = $arguments[0]->evaluate($scope);
				
			if (count($arguments) == 1) { // (#? 'propertyName')
				if (!array_key_exists(0, $scope->arguments)) {
					throw new \BadFunctionCallException("PropertyExists: Expected value of type array/object as second parameter but none found.");
				}
		
				$useWrappedArgument = true;
			}
			else { // (#? 'propertyName' _obj)
				$value = $arguments[1]->evaluate($scope);
			}
		}
		else {
			$property = $this->property;
				
			if (count($arguments) == 0) { // (#propertyName?)
				if (!array_key_exists(0, $scope->arguments)) {
					throw new \BadFunctionCallException("PropertyExists: Expected value of type array/object as first parameter but none found.");
				}
		
				$useWrappedArgument = true;
			}
			else { // (#propertyName? _obj)
				$value = $arguments[0]->evaluate($scope);
			}
		}
		
		//check property using the wrapped argument defined in the environment
		if ($useWrappedArgument) {
			if (is_null($scope->wrappedArgument)) {
				throw new \BadFunctionCallException("PropertyExists: Expected value of type array/object as first parameter but none found.");
			}
			
			return $scope->wrappedArgument->offsetExists($property);
		}
		
		//check value type
		if (!is_array($value) && !is_object($value)) {
			throw new \InvalidArgumentException(sprintf("PropertyExists: Expected value of type array/object but %s found instead", gettype($value)));
		}
		
		return ParameterWrapper::wrapValue($value)->offsetExists($property);
	}
}
?>