<?php
namespace eMapper\Cache\Key;

use eMapper\Type\TypeManager;
use eMapper\Type\TypeHandler;
use eMapper\Type\ValueExport;
use eMapper\Reflection\Profiler;
use eMapper\Reflection\Parameter\ParameterWrapper;

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
	
	/**
	 * First argument property list
	 * @var array
	 */
	public $propertyList;
	
	/**
	 * Key arguments
	 * @var array
	 */
	public $args;
	
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
	protected function castArray($value, TypeHandler $typeHandler, $join_string = ',') {
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
				return $this->castArray($value, $typeHandler, '_');
			}
		}
		else {
			//cast value to the specified type
			$typeHandler = $this->typeManager->getTypeHandler($type);
				
			if (is_array($value)) {
				return $this->castArray($value, $typeHandler, '_');
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
	 * Obtains the default type handler to use for a given property
	 * @param ParameterWrapper $arg
	 * @param string $property
	 * @return NULL | string
	 */
	protected function getDefaultType(ParameterWrapper $arg, $property) {
		$type = null;
		
		if (array_key_exists($property, $arg->config)) {
			if (array_key_exists('type', $arg->config[$property])) {
				$type = $arg->config[$property]['type'];
			}
			elseif (array_key_exists('var', $arg->config[$property])) {
				$type = $arg->config[$property]['var'];
			
				if ($this->typeManager->getTypeHandler($type) === false) {
					$type = null;
				}
			}
		}
		
		return $type;
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
		if (!$arg->offsetExists($property)) {
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
	 * Obtains an element within an object/property by a given subindex
	 * @param mixed $value
	 * @param string $subindex
	 * @param string $type
	 * @throws \InvalidArgumentException
	 * @throws \UnexpectedValueException
	 * @throws \OutOfBoundsException
	 * @return string
	 */
	protected function getSubIndex($value, $subindex = null, $type = null) {
		if (is_null($subindex)) {
			return is_null($value) ? 'NULL' : $this->castParameter($value, $type);
		}
		
		//check if the value is null
		if (is_null($value)) {
			throw new \InvalidArgumentException("Trying to obtain a value from NULL parameter on index $index");
		}
		
		if (is_array($value) || is_object($value)) {
			//build wrapper
			if (!($value instanceof ParameterWrapper)) {
				$value = ParameterWrapper::wrap($value);
			}
			
			if (!$value->offsetExists($subindex)) {
				throw new \UnexpectedValueException("Property '$subindex' not found on given parameter");
			}
			
			$type = $this->getDefaultType($value, $subindex);
			return $this->castParameter($value[$subindex], $type);
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
		if (!$arg->offsetExists($property)) {
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
	 * @param mixed $value
	 * @param int $left_index
	 * @param int $right_index
	 * @param string $type
	 * @throws \InvalidArgumentException
	 * @return string
	 */
	protected function getSubRange($value, $left_index, $right_index, $type = null) {		
		//check if both indexes are empty
		if (empty($left_index) && strlen($right_index) == 0) {
			if (is_null($value)) {
				return 'NULL';
			}
		
			return $this->castParameter($value, $type);
		}
		
		//check if value is null
		if (is_null($value)) {
			throw new \InvalidArgumentException("Trying to obtain a value from NULL parameter on index $index");
		}
		
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
		$this->args = $args;
		$counter_start = 0;
		
		//replace configuration propeties expressions
		if (preg_match(self::CONFIG_REGEX, $expr)) {
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
			//validate argument
			if (empty($args[0])) {
				throw new \InvalidArgumentException("No valid parameters have been defined for this query");
			}
			elseif (!is_object($args[0]) && !is_array($args[0])) {
				throw new \InvalidArgumentException("Specified parameter is not an array/object");
			}
			
			$counter_start = 1;
			
			//wrap first argument
			$this->args[0] = ParameterWrapper::wrap($args[0], $this->parameterMap);

			$expr = preg_replace_callback(self::PROPERTY_PARAM_REGEX, 
					function ($matches) {
						$total_matches = count($matches);
						$type = $subindex = null;
						
						switch ($total_matches) {
							/**
							 * Property
							 */
							case 4: //#{PROPERTY@1[INDEX]@2?:TYPE@3}
								$type = substr($matches[3], 1);
							case 3: //#{PROPERTY@1[INDEX]@2}
								$subindex = empty($matches[2]) ? null : substr($matches[2], 1, -1);
							case 2: //#{PROPERTY@1}
								$key = $matches[1];
								
								if (is_null($type) && isset($this->parameterMap)) {
									$type = $this->getDefaultType($this->args[0], $key);
								}
								
								return $this->getIndex($this->args[0], $key, $subindex, $type);
								break;
							
							/**
							 * Interval
							 */
							case 9: //#{PROPERTY@4[LEFT_INDEX@6..RIGHT_INDEX@7]:TYPE@8}
								$type = substr($matches[8], 1);
							case 8: //#{PROPERTY@4[LEFT_INDEX@6..RIGHT_INDEX@7]}
								$key = $matches[4];
								
								if (is_null($type) && isset($this->parameterMap)) {
									$type = $this->getDefaultType($this->args[0], $key);
								}
								
								return $this->getRange($this->args[0], $key, $matches[6], $matches[7], $type);
								break;
						}
						
					}, $expr);
		}
		
		//replace inline parameter
		if (!empty($args) && preg_match(self::INLINE_PARAM_REGEX, $expr)) {
			$total_args = count($this->args);
			$expr = preg_replace_callback(self::INLINE_PARAM_REGEX,
					function ($matches) use ($counter_start, $total_args) {
						$total_matches = count($matches);
						
						if ($total_matches == 2) { //%{TYPE@1 | CLASS@1}
							//check if there is arguments left
							if ($counter_start >= $total_args) {
								throw new \OutOfBoundsException("No arguments left for expression '{$matches[0]}'");
							}
								
							return $this->castParameter($this->args[$counter_start++], $matches[1]);
						}
						else {
							$subindex = $type = null;
							
							switch ($total_matches) {
								/**
								 * Simple index
								 */
								case 5: //%{NUMBER@2[INDEX]@3?:TYPE@4}
									$type = substr($matches[4], 1);
								case 4: //%{NUMBER@2[INDEX]@3}
									$subindex = empty($matches[3]) ? null : substr($matches[3], 1, -1);
								case 3: //%{NUMBER@2}
									$index = intval($matches[2]);
									
									if (!array_key_exists($index, $this->args)) {
										throw new \InvalidArgumentException("No value found on index $index");
									}
									
									return $this->getSubIndex($this->args[$index], $subindex, $type);
									
									break;
								
								/**
								 * Interval
								 */
								case 10: //%{NUMBER@5[LEFT@7?..RIGHT@8?]:TYPE@9}
									$type = substr($matches[9], 1);
								case 9: //%{NUMBER@5[LEFT@7?..RIGHT@8?]}
									$index = intval($matches[5]);
									
									if (!array_key_exists($index, $this->args)) {
										throw new \InvalidArgumentException("No value found on index $index");
									}
									
									return $this->getSubRange($this->args[$index], $matches[7], $matches[8], $type);
									break;
							}
						}
					}, $expr);
		}
		
		return $expr;
	}
}
?>