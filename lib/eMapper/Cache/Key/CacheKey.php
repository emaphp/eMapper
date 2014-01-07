<?php
namespace eMapper\Cache\Key;

use eMapper\Type\TypeManager;
use eMapper\Type\TypeHandler;
use eMapper\Type\ValueExport;
use eMapper\Reflection\Profiler;

class CacheKey {
	use ValueExport;
	
	//Ex: @{my_var}
	const CONFIG_REGEX = '/@{([\w|\.]+)}/';
	//Ex: %{type}, %{#nargument}, %{#nargument:type}
	const INLINE_PARAM_REGEX = '@%{([A-z]{1}[\w|\\\\]*)}|%{(\d+)(\[\w+\])?(:[A-z]{1}[\w|\\\\]*)?}|%{(\d+)(\[(-?\d+|)\.\.(-?\d+|)\])?(:[A-z]{1}[\w|\\\\]*)?}@';
	//Ex: #{property}, #{property:type}
	const PROPERTY_PARAM_REGEX = '@#{([A-z|_]{1}[\w|\\\\]*)(\[\w+\])?(:[A-z]{1}[\w|\\\\]*)?}|#{([A-z|_]{1}[\w|\\\\]*)(\[(-?\d+|)\.\.(-?\d+|)\])?(:[A-z]{1}[\w|\\\\]*)?}@';
	
	/**
	 * Type manager
	 * @var TypeManager
	 */
	public $typeManager;
	
	/**
	 * Parameter map
	 * @var object
	 */
	public $parameterMap;
	
	public function __construct(TypeManager $typeManager, $parameterMap = null) {
		$this->typeManager = $typeManager;
		$this->parameterMap = $parameterMap;
	}
	
	/**
	 * Casts all elements in an array with the given type handler
	 * @param mixed $value
	 * @param TypeHandler $typeHandler
	 * @param string $join_string
	 * @param string $escape_string
	 * @return string
	 */
	protected function castArray($value, TypeHandler $typeHandler, $join_string = ',', $escape_string = true) {
		$list = array();

		//build expression list
		foreach ($value as $val) {
			$val = $typeHandler->castParameter($val);
		
			if (is_null($val)) {
				$new_elem = 'NULL';
			}
			else {
				$new_elem = $typeHandler->setParameter($val);
		
				if (is_null($new_elem)) {
					$new_elem = 'NULL';
				}
				elseif (!is_string($new_elem)) {
					$new_elem = (string) $new_elem;
				}
			}
			
			$list[] = $new_elem;
		}

		//return joined expression
		return implode($join_string, $list);
	}
	
	/**
	 * Casts a value to a type
	 * @param mixed $value
	 * @param string $type
	 * @return string
	 */
	protected function castParameter($value, $type = null) {
		//check null value
		if (is_null($value)) {
			return 'NULL';
		}
		elseif (is_null($type)) {
			//obtain default type handler
			$typeHandler = $this->getDefaultTypeHandler($value);
	
			if (is_null($typeHandler)) {
				return 'NULL';
			}
				
			if (is_array($value)) {
				return $this->castArray($value, $typeHandler, '_', false);
			}
		}
		else {
			//cast value to the specified type
			$typeHandler = $this->typeManager->getTypeHandler($type);
				
			if (is_array($value)) {
				return $this->castArray($value, $typeHandler, '_', false);
			}
			else {
				$value = $typeHandler->castParameter($value);
	
				//check if returned value is null
				if (is_null($value)) {
					return 'NULL';
				}
			}
		}
	
		//get parameter expression
		$value = $typeHandler->setParameter($value);
	
		if (is_null($value)) {
			return 'NULL';
		}
		//cast to string
		elseif (!is_string($value)) {
			$value = (string) $value;
		}
	
		return $value;
	}
	
	/**
	 * Obtains an element within an object/property by a given index
	 * @param mixed $arg
	 * @param mixed $property
	 * @param string $index
	 * @param string $type
	 * @throws \InvalidArgumentException
	 * @throws \UnexpectedValueException
	 * @throws \OutOfBoundsException
	 * @return string
	 */
	protected function getIndex($arg, $property, $index = null, $type = null) {
		//verify valid property
		if (!array_key_exists($property, $arg)) {
			throw new \InvalidArgumentException("Unknown property '$property'");
		}
		
		if (is_null($index)) {
			return $this->castParameter($arg[$property], $type);
		}
		
		//check if the value is null
		if (is_null($arg[$property])) {
			throw new \InvalidArgumentException("Trying to obtain value from NULL property '$property'");
		}
		
		$value = $arg[$property];
		
		//check value type
		if (is_array($value)) {
			//search for string index
			if (array_key_exists($index, $value)) {
				return $this->castParameter($value[$index], $type);
			}
			//try numeric index instead
			elseif (is_numeric($index) && array_key_exists((int) $index, $value)) {
				return $this->castParameter($value[(int) $index], $type);
			}
		
			throw new \UnexpectedValueException("Index '$index' does not exists in property '$property'");
		}
		//try to convert value to string
		elseif (($value = $this->toString($value)) !== false) {
			//check string length against index
			if ($index < strlen($value)) {
				return $this->castParameter($value[$index], $type);
			}
		
			throw new \OutOfBoundsException("Index '$index' out of bounds for property '$property'");
		}
			
		throw new \InvalidArgumentException("Cannot obtain index '$index' from a non array/string property '$property'");
	}
	
	/**
	 * Obtains a property from a given object
	 * @param object $instance
	 * @param string $property
	 * @throws \InvalidArgumentException
	 * @return mixed
	 */
	protected function obtainProperty($instance, $property) {
		$class = get_class($instance);
		
		if (Profiler::isEntity($class)) {
			$properties = Profiler::getClassProperties($class);
			$map = $this->buildParameterMap($instance, $properties, new \ReflectionClass($class));
		}
		elseif (isset($this->parameterMap)) {
			$properties = Profiler::getClassProperties($this->parameterMap);
			$map = $this->buildParameterMap($instance, $properties, new \ReflectionClass($class));
		}
		else {
			$map = get_object_vars($instance);
		}
		
		if (!array_key_exists($property, $map)) {
			throw new \InvalidArgumentException("Property $property not found in instance of $class");
		}
		
		return $map[$property];
	}
	
	/**
	 * Obtains an element within an object/property by a given subindex
	 * @param mixed $args
	 * @param mixed $index
	 * @param string $subindex
	 * @param string $type
	 * @throws \InvalidArgumentException
	 * @throws \UnexpectedValueException
	 * @throws \OutOfBoundsException
	 * @return string
	 */
	protected function getSubIndex($args, $index, $subindex = null, $type = null) {
		//check if that argument index holds a value
		if (!array_key_exists($index, $args)) {
			throw new \InvalidArgumentException("No value found on index $index");
		}
		
		if (is_null($subindex)) {
			//check if the value is null
			if (is_null($args[$index])) {
				return 'NULL';
			}
				
			return $this->castParameter($args[$index], $type);
		}
		
		//check if the value is null
		if (is_null($args[$index])) {
			throw new \InvalidArgumentException("Trying to obtain a value from NULL parameter on index $index");
		}
		
		$value = $args[$index];
		
		//check value type
		if (is_array($value) || $value instanceof \ArrayObject) {
			//search for string index
			if (array_key_exists($subindex, $value)) {
				return $this->castParameter($value[$subindex], $type);
			}
			//try numeric index instead
			elseif (is_numeric($subindex) && array_key_exists((int) $subindex, $value)) {
				return $this->castParameter($value[(int) $subindex], $type);
			}
		
			throw new \UnexpectedValueException("Index '$subindex' does not exists in argument $index");
		}
		elseif (is_object($value)) {
			return $this->castParameter($this->obtainProperty($value, $subindex), $type);
		}
		elseif (($value = $this->toString($value)) !== false) {
			//check string length against index
			if ($subindex < strlen($value)) {
				return $this->castParameter($value[$subindex], $type);
			}
		
			throw new \OutOfBoundsException("Index '$subindex' out of bounds for argument $index");
		}
		
		throw new \InvalidArgumentException("Cannot obtain index '$subindex' from a non-array type");
	}
	
	/**
	 * Obtains a range of elements from within an array/string
	 * @param mixed $arg
	 * @param mixed $property
	 * @param int $left_index
	 * @param int $right_index
	 * @param string $type
	 * @throws \InvalidArgumentException
	 * @return string
	 */
	protected function getRange($arg, $property, $left_index, $right_index, $type = null) {
		//verify valid property
		if (!array_key_exists($property, $arg)) {
			throw new \InvalidArgumentException("Unknown property '$property'");
		}
		
		//check if both indexes are empty
		if (empty($left_index) && strlen($right_index) == 0) {
			return $this->castParameter($arg[$property], $type);
		}
		
		//check if the value is null
		if (is_null($arg[$property])) {
			throw new \InvalidArgumentException("Trying to obtain value from NULL parameter on property '$property'");
		}
		
		$value = $arg[$property];
		
		//check value type
		if (is_array($value)) {
			//get array slice
			$right_index = strlen($right_index) == 0 ? null : (int) $right_index;
			return $this->castParameter(array_slice($value, (int) $left_index, $right_index), $type);
		}
		//try to convert value to string
		elseif (($value = $this->toString($value)) !== false) {
			if (strlen($right_index) == 0) {
				return $this->castParameter(substr($value, (int) $left_index), $type);
			}
				
			return $this->castParameter(substr($value, (int) $left_index, (int) $right_index), $type);
		}
			
		throw new \InvalidArgumentException("Cannot obtain indexes from non-array property '$property'");
	}
	
	/**
	 * Obtains a subrange of elements from within an array/string
	 * @param mixed $args
	 * @param mixed $index
	 * @param int $left_index
	 * @param int $right_index
	 * @param string $type
	 * @throws \InvalidArgumentException
	 * @return string
	 */
	protected function getSubRange($args, $index, $left_index, $right_index, $type = null) {
		//check if that argument index holds a value
		if (!array_key_exists($index, $args)) {
			throw new \InvalidArgumentException("No value found on index $index");
		}
		
		//check if both indexes are empty
		if (empty($left_index) && strlen($right_index) == 0) {
			if (is_null($args[$index])) {
				return 'NULL';
			}
		
			return $this->castParameter($args[$index], $type);
		}
		
		//check if value is null
		if (is_null($args[$index])) {
			throw new \InvalidArgumentException("Trying to obtain a value from NULL parameter on index $index");
		}
		
		$value = $args[$index];
		
		//check value type
		if (is_array($value) || $value instanceof \ArrayObject) {
			$right_index = strlen($right_index) == 0 ? null : (int) $right_index;
			return $this->castParameter(array_slice($value, (int) $left_index, $right_index), $type);
		}
		elseif (($value = $this->toString($value)) !== false) {
			if (strlen($right_index) == 0) {
				return $this->castParameter(substr($value, (int) $left_index), $type);
			}
			else {
				return $this->castParameter(substr($value, (int) $left_index, (int) $right_index), $type);
			}
		}
			
		throw new \InvalidArgumentException("Cannot obtain indexes from a non-array type");
	}
	
	/**
	 * Obtains the default type handler for a given type
	 * @param unknown $value
	 * @throws \RuntimeException
	 * @return NULL|TypeHandler
	 */
	protected function getDefaultTypeHandler($value) {
		//get value type
		$type = gettype($value);
		
		switch ($type) {
			case 'null':
				return null;
				break;
					
			case 'array':
				//check empty array
				if (count($value) == 0) {
					return null;
				}
				//get first value type hanlder
				elseif (count($value) == 1) {
					return $this->getDefaultTypeHandler(current($value));
				}
				else {
					$typeHandler = null;
						
					//obtain type by checking inner values
					foreach ($value as $val) {
						$typeHandler = $this->getDefaultTypeHandler($val);
		
						//get type of the first not null value
						if (!is_null($typeHandler)) {
							break;
						}
					}
						
					return $typeHandler;
				}
				break;
		
			case 'object':
				//get object class
				$classname = get_class($value);
		
				//use class as type
				$typeHandler = $this->typeManager->getTypeHandler($classname);
		
				if ($typeHandler !== false) {
					return $typeHandler;
				}
		
				throw new \RuntimeException("No default type handler found for class '$classname'");
				break;
		
			case 'resource':
				//unsupported, throw exception
				throw new \RuntimeException("Argument of type 'resource' is not supported");
				break;
		
			default:
				//generic type
				return $this->typeManager->getTypeHandler($type);
				break;
		}
	}
	
	/**
	 * Extracts all values associated from a parameter map
	 * @param object $instance
	 * @param array $properties
	 * @param \ReflectionClass $reflectionClass
	 * @throws \UnexpectedValueException
	 * @return array
	 */
	protected function buildParameterMap($instance, $properties, $reflectionClass) {
		$map = array();
			
		foreach ($properties as $k => $v) {
			//obtain property though getter method
			if ($v->has('getter')) {
				$getter = $v->get('getter');
					
				if (!$reflectionClass->hasMethod($getter)) {
					throw new \UnexpectedValueException(sprintf("Getter method $getter not found in class %s", get_class($instance)));
				}
				else {
					$method = $reflectionClass->getMethod($getter);
		
					if (!$method->isPublic()) {
						throw new \UnexpectedValueException(sprintf("Getter method $getter does not have public access in class %s", get_class($instance)));
					}
				}
					
				$map[$k] = $instance->$getter();
			}
			elseif (!($instance instanceof \stdClass)) {
				$property = $v->has('property') ? $v->get('property') : $k;
		
				if (!$reflectionClass->hasProperty($property)) {
					throw new \UnexpectedValueException(sprintf("Property $property was not found on class %s", get_class($instance)));
				}
					
				$prop = $reflectionClass->getProperty($property);
					
				if (!$prop->isPublic()) {
					throw new \UnexpectedValueException(sprintf("Property $property does not have public access on class %s", get_class($instance)));
				}
		
				$map[$k] = $instance->$property;
			}
			else {
				$property = $v->has('property') ? $v->get('property') : $k;
				
				if (!property_exists($instance, $property)) {
					throw new \UnexpectedValueException(sprintf("Property $property was not found on class %s", get_class($instance)));
				}
				
				$map[$k] = $instance->$property;
			}
		}
		
		return $map;
	}
	
	/**
	 * Parses a string and replaces all references to arguments
	 * @param string $expr
	 * @param array $args
	 * @param array $config
	 * @throws \UnexpectedValueException
	 * @throws \InvalidArgumentException
	 * @throws \OutOfBoundsException
	 * @return string
	 */
	public function build($expr, $args, $config) {
		//replace configuration propeties expressions
		if (preg_match(self::CONFIG_REGEX, $expr)) {
			/**
			 * Configuration properties replacing
			 */
			$expr = preg_replace_callback(self::CONFIG_REGEX,
					function ($matches) use ($config) {
						$property = $matches[1];
			
						//check key existence
						if (!array_key_exists($property, $config)) {
							throw new \InvalidArgumentException("Unknown configuration value '$property'");
						}
			
						//convert to string, if possible
						if (($str = $this->toString($config[$property])) === false) {
							throw new \InvalidArgumentException("Configuration property '$property' could not be converted to string");
						}
			
						return $str;
					}, $expr);
		}
		
		if (preg_match(self::PROPERTY_PARAM_REGEX, $expr)) {
			if (empty($args[0])) {
				throw new \InvalidArgumentException("No valid parameters have been defined for this query");
			}
			elseif (!is_object($args[0]) && !is_array($args[0])) {
				throw new \InvalidArgumentException("Specified parameter is not an array/object");
			}
			
			$properties = null;
			
			//obtain object properties as an array
			if (is_object($args[0]) && !($args[0] instanceof \ArrayObject)) {
				$class = get_class($args[0]);
				
				if (Profiler::isEntity($class)) {
					$properties = Profiler::getClassProperties($class);
					$args[0] = $this->buildParameterMap($args[0], $properties, new \ReflectionClass($class));
				}
				elseif (isset($this->parameterMap)) {
					$properties = Profiler::getClassProperties($this->parameterMap);
					$args[0] = $this->buildParameterMap($args[0], $properties, new \ReflectionClass($class));
				}
				else {
					$args[0] = get_object_vars($args[0]);
				}
			}
			elseif (isset($this->parameterMap)) {
				//build argument from parameter class
				$properties = Profiler::getClassProperties($this->parameterMap);
				$map = array();
				
				foreach ($properties as $k => $v) {
					$property = $v->has('property') ? $v->get('property') : $k;
					
					if (!array_key_exists($property, $args[0])) {
						throw new \UnexpectedValueException("Unknown field $property defined in parameter map class {$this->parameterMap}");
					}
					
					$map[$k] = $args[0][$property];
				}
				
				$args[0] = $map;
			}
			
			/**
			 * Parameter properties replacing
			 */
			$expr = preg_replace_callback(self::PROPERTY_PARAM_REGEX, 
					function ($matches) use ($args, $properties) {
						$total_matches = count($matches);
			
						if ($total_matches == 2) { //#{PROPERTY@1}
							$key = $matches[1];
							
							//use type declared in parameter map by default
							if (isset($properties) && array_key_exists($key, $properties) && $properties[$key]->has('type')) {
								return $this->getIndex($args[0], $key, null, $properties[$key]->get('type'));
							}
							
							return $this->getIndex($args[0], $key);
						}
						elseif ($total_matches == 3) { //#{PROPERTY@1[INDEX]@2}
							$key = $matches[1];
								
							//use type declared in parameter map by default
							if (isset($properties) && array_key_exists($key, $properties) && $properties[$key]->has('type')) {
								return $this->getIndex($args[0], $key, substr($matches[2], 1, -1), $properties[$key]->get('type'));
							}
							
							return $this->getIndex($args[0], $key, substr($matches[2], 1, -1));
						}
						elseif ($total_matches == 4) { //#{PROPERTY@1[INDEX]@2?:TYPE@3}
							$type = substr($matches[3], 1);
								
							if (empty($matches[2])) {
								return $this->getIndex($args[0], $matches[1], null, $type);
							}
								
							return $this->getIndex($args[0], $matches[1], substr($matches[2], 1, -1), $type);
						}
						elseif ($total_matches == 8) { //#{PROPERTY@4[LEFT_INDEX@6..RIGHT_INDEX@7]}
							$key = $matches[4];
							
							//use type declared in parameter map by default
							if (isset($properties) && array_key_exists($key, $properties) && $properties[$key]->has('type')) {
								return $this->getRange($args[0], $key, $matches[6], $matches[7], $properties[$key]->get('type'));
							}
							
							return $this->getRange($args[0], $key, $matches[6], $matches[7]);
						}
						else { //#{PROPERTY@4[LEFT_INDEX@6..RIGHT_INDEX@7]:TYPE@8}
							return $this->getRange($args[0], $matches[4], $matches[6], $matches[7], substr($matches[8], 1));
						}
					}, $expr);
		}
		
		//replace inline parameter
		if (!empty($args) && preg_match(self::INLINE_PARAM_REGEX, $expr)) {
			/**
			 * Inline parameters replacing
			 */
			$expr = preg_replace_callback(self::INLINE_PARAM_REGEX,
					function ($matches) use ($args) {
						static $args_counter = 0;
						$total_args = count($args);
						$total_matches = count($matches);
						
						if ($total_matches == 2) { //%{TYPE@1 | CLASS@1}
							//check if there is arguments left
							if ($args_counter >= $total_args) {
								throw new \OutOfBoundsException("No arguments left for expression '{$matches[0]}'");
							}
								
							return $this->castParameter($args[$args_counter++], $matches[1]);
						}
						elseif ($total_matches == 3) { //%{NUMBER@2}
							return $this->getSubIndex($args, (int) $matches[2]);
						}
						elseif ($total_matches == 4) { //%{NUMBER@2[INDEX]@3}
							return $this->getSubIndex($args, (int) $matches[2], substr($matches[3], 1, -1));
						}
						elseif ($total_matches == 5) { //%{NUMBER@2[INDEX]@3?:TYPE@4}
							//get argument index
							$index = (int) $matches[2];
							//get type
							$type = substr($matches[4], 1);
								
							//check if index is specified
							if (empty($matches[3])) {
								return $this->getSubIndex($args, $index, null, $type);
							}
			
							return $this->getSubIndex($args, $index, substr($matches[3], 1, -1), $type);
						}
						elseif ($total_matches == 9) { //%{NUMBER@5[LEFT@7?..RIGHT@8?]}
							return $this->getSubRange($args, (int) $matches[5], $matches[7], $matches[8]);
						}
						else { //%{NUMBER@5[LEFT@7?..RIGHT@8?]:TYPE@9}
							return $this->getSubRange($args, (int) $matches[5], $matches[7], $matches[8], $type = substr($matches[9], 1));
						}
					}, $expr);
		}
		
		return $expr;
	}
}
?>