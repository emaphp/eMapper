<?php
namespace eMapper\Dynamic\Runtime;

use eMacros\Applicable;
use eMacros\Scope;
use eMacros\GenericList;
use eMapper\Reflection\Profiler;

class PropertyGet implements Applicable {
	/**
	 * Property to obtain
	 * @var mixed
	 */
	public $property;
	
	public function __construct($property = null) {
		$this->property = $property;
	}
	
	public function apply(Scope $scope, GenericList $arguments) {
		//get property and value
		if (is_null($this->property)) {
			if (empty($arguments)) {
				throw new \BadFunctionCallException("PropertyGet: No parameters found.");
			}
			
			$property = $arguments[0]->evaluate($scope);
			
			if (count($arguments) == 1) {
				if (!array_key_exists(0, $scope->arguments)) {
					throw new \BadFunctionCallException("PropertyGet: Expected value of type array/object as second parameter but none found.");
				}
				
				$value = $scope->arguments[0];
			}
			else {
				$value = $arguments[1]->evaluate($scope);
			}
		}
		else {
			$property = $this->property;
			
			if (empty($arguments)) {
				if (!array_key_exists(0, $scope->arguments)) {
					throw new \BadFunctionCallException("PropertyGet: Expected value of type array/object as first parameter but none found.");
				}
				
				$value = $scope->arguments[0];
			}
			else {
				$value = $arguments[0]->evaluate($scope);
			}
		}
		
		//get index value
		if (is_array($value)) {
			if (!array_key_exists($property, $value)) {
				if (is_int($property)) {
					throw new \OutOfBoundsException(sprintf("PropertyGet: Key %s does not exists.", strval($property)));
				}
		
				throw new \InvalidArgumentException(sprintf("PropertyGet: Key '%s' does not exists.", strval($property)));
			}
		
			return $value[$property];
		}
		//get index value from object
		elseif ($value instanceof \ArrayObject || $value instanceof \ArrayAccess) {
			if (!$value->offsetExists($property)) {
				if (is_int($property)) {
					throw new \OutOfBoundsException(sprintf("PropertyGet: Key %s does not exists.", strval($property)));
				}
		
				throw new \InvalidArgumentException(sprintf("PropertyGet: Key '%s' does not exists.", strval($property)));
			}
		
			return $value[$property];
		}
		//get property value
		elseif (is_object($value)) {
			$classname = get_class($value);
			
			if (Profiler::isEntity($classname)) {
				$properties = Profiler::getClassProperties($classname);
				
				if (!array_key_exists($property, $properties)) {
					throw new \InvalidArgumentException("PropertyGet: Property '$property' does not exists in class $classname");
				}
				
				$annotations = $properties[$property];
				$reflectionClass = Profiler::getReflectionClass($classname);
				
				if ($annotations->has('getter')) {
					$getter = $annotations->get('getter');
					
					if (!$reflectionClass->hasMethod($getter)) {
						throw new \InvalidArgumentException("PropertyGet: Getter method '$getter' is not available in class $classname");
					}
					
					$method = $reflectionClass->getMethod($getter);
					
					if (!$method->isPublic()) {
						throw new \UnexpectedValueException("PropertyGet: Getter method '$getter' is not accessible in class $classname");
					}
					
					return $value->$getter();
				}
				else {
					$prop = $reflectionClass->getProperty($property);
					
					if (!$prop->isPublic()) {
						throw new \UnexpectedValueException("PropertyGet: Property '$property' is not accessible in class $classname");
					}
					
					return $value->$value;
				}
			}
			else {
				if (!property_exists($value, $property)) {
					//check existence through __isset
					if (method_exists($value, '__isset') && !$value->__isset($property)) {
						throw new \InvalidArgumentException(sprintf("PropertyGet: Property '%s' not found.", strval($property)));
					}
				
					//try calling __get
					if (method_exists($value, '__get')) {
						return $value->__get($property);
					}
				
					throw new \InvalidArgumentException(sprintf("PropertyGet: Property '%s' not found.", strval($property)));
				}
				
				//check property access
				$rp = new \ReflectionProperty($value, $property);
				
				if (!$rp->isPublic()) {
					throw new \InvalidArgumentException(sprintf("PropertyGet: Cannot access non-public property '%s'.", strval($property)));
				}
				
				return $value->$property;
			}
		}
		
		throw new \InvalidArgumentException(sprintf("PropertyGet: Expected value of type array/object but %s found instead", gettype($value)));
	}
}
?>