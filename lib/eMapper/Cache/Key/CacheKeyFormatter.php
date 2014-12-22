<?php
namespace eMapper\Cache\Key;

use eMapper\Type\ToString;
use eMapper\Type\TypeManager;
use eMapper\Type\TypeHandler;
use eMapper\Reflection\Argument\ArgumentWrapper;
use eMapper\Reflection\Argument\ObjectArgumentWrapper;

/**
 * The CacheKeyFormatter class generates the cache id string used to store a value in cache.
 * @author emaphp
 */
class CacheKeyFormatter {
	use ToString;
	
	//Ex: @{my_var}
	const CONFIG_REGEX = '/@{([\w|\.]+)}/';
	//Ex: %{type}, %{#nargument}, %{#nargument:type}, %{#nargument[subindex]:type}
	const INLINE_PARAM_REGEX = '@%{([A-z]{1}[\w|\\\\]*)}|%{(\d+)(\[\w+\])?(:[A-z]{1}[\w|\\\\]*)?}|%{(\d+)(\[(-?\d+|)\.\.(-?\d+|)\])?(:[A-z]{1}[\w|\\\\]*)?}@';
	//Ex: #{property}, #{property:type}
	const PROPERTY_PARAM_REGEX = '@#{([A-z|_]{1}[\w|\\\\]*)(\[\w+\])?(:[A-z]{1}[\w|\\\\]*)?}|#{([A-z|_]{1}[\w|\\\\]*)(\[(-?\d+|)\.\.(-?\d+|)\])?(:[A-z]{1}[\w|\\\\]*)?}@';
	//Ex: @@users
	const SHORT_PREFIX = '@@';
	
	/**
	 * Type manager
	 * @var \eMapper\Type\TypeManager
	 */
	protected $typeManager;
		
	/**
	 * Key arguments
	 * @var array
	 */
	protected $args;
	
	/**
	 * Counfiguration values
	 * @var array
	 */
	protected $config;
	
	/**
	 * Internal counter
	 * @var integer
	 */
	protected $counter;
	
	/**
	 * Wrapped argument
	 * @var \eMapper\Reflection\Argument\ArgumentWrapper
	 */
	protected $wrappedArg;
	
	public function __construct(TypeManager $typeManager) {
		$this->typeManager = $typeManager;
	}

	/**
	 * Obtains the default type handler for a given value
	 * @param mixed $value
	 * @throws \RuntimeException
	 * @return NULL | TypeHandler
	 */
	protected function getDefaultTypeHandler($value) {
		//get value type
		$type = gettype($value);
	
		switch ($type) {
			case 'null':
				return null;
			case 'array':
				//check empty array
				if (count($value) == 0)
					return null;
				elseif (count($value) == 1) //get first value type hanlder
					return $this->getDefaultTypeHandler(current($value));
				
				$typeHandler = null;
				
				//obtain type by checking inner values
				foreach ($value as $val) {
					$typeHandler = $this->getDefaultTypeHandler($val);
					//get type of the first not null value
					if (!is_null($typeHandler))
						break;
				}
				
				return $typeHandler;

			case 'object':
				//get object class
				$classname = get_class($value);	
				//use class as type
				$typeHandler = $this->typeManager->getTypeHandler($classname);
				if ($typeHandler !== false)
					return $typeHandler;
				//no typehandler found, throw exception
				throw new \RuntimeException("No default type handler found for class '$classname'");
	
			case 'resource':
				//unsupported, throw exception
				throw new \RuntimeException("Argument of type 'resource' is not supported");

			default:
				//generic type
				return $this->typeManager->getTypeHandler($type);
		}
	}
	
	/**
	 * Casts all elements in an array with the given type handler
	 * @param array $value
	 * @param TypeHandler $typeHandler
	 * @param string $join_string
	 * @return string
	 */
	protected function castArray($value, TypeHandler $typeHandler, $join_string = ',') {
		$list = [];
	
		//build expression list
		foreach ($value as $val) {
			$val = $typeHandler->castParameter($val);
	
			if (is_null($val))
				$new_elem = 'NULL';
			else {
				$new_elem = $typeHandler->setParameter($val);
				if (is_null($new_elem))
					$new_elem = 'NULL';
				elseif (!is_string($new_elem))
					$new_elem = strval($new_elem);
			}
				
			$list[] = $new_elem;
		}
	
		//return joined expression
		return implode($join_string, $list);
	}
	
	/**
	 * Casts a value to a given type
	 * @param mixed $value
	 * @param string $type
	 * @return string
	 * @throws \RuntimeException
	 */
	protected function castParameter($value, $type = null) {
		//check null value
		if (is_null($value))
			return 'NULL';
		elseif (is_null($type)) {
			//obtain default type handler
			$typeHandler = $this->getDefaultTypeHandler($value);
			if (is_null($typeHandler))
				return 'NULL';
			if (is_array($value))
				return $this->castArray($value, $typeHandler, '_');
		}
		else {
			//cast value to the specified type
			$typeHandler = $this->typeManager->getTypeHandler($type);
				
			if ($typeHandler === false)
				throw new \RuntimeException("No type handler found for type '$type'");
				
			if (is_array($value))
				return $this->castArray($value, $typeHandler, '_');
			
			//check if returned value is null
			$value = $typeHandler->castParameter($value);
			if (is_null($value))
				return 'NULL';
		}
	
		//get parameter expression
		$value = $typeHandler->setParameter($value);
	
		//check null value
		if (is_null($value))
			return 'NULL';
		//cast to string
		if (!is_string($value))
			$value = strval($value);
		return $value;
	}
	
	/**
	 * Obtains an element within an object/property by a given index
	 * @param mixed $property
	 * @param string $index
	 * @param string $type
	 * @throws \InvalidArgumentException
	 * @throws \OutOfBoundsException
	 * @return string
	 */
	protected function getIndex($property, $index = null, $type = null) {
		//verify valid property
		if (!$this->wrappedArg->offsetExists($property))
			throw new \InvalidArgumentException("Unknown property '$property'");
	
		if (is_null($index))
			return $this->castParameter($this->wrappedArg->offsetGet($property), $type);
	
		//check if the value is null
		$value = $this->wrappedArg->offsetGet($property);
		if (is_null($value))
			throw new \InvalidArgumentException("Trying to obtain value from NULL property '$property'");
	
		//check value type
		if (is_array($value)) {
			//search for string index
			if (array_key_exists($index, $value))
				return $this->castParameter($value[$index], $type);
			//try numeric index instead
			elseif (is_numeric($index) && array_key_exists(intval($index), $value))
				return $this->castParameter($value[intval($index)], $type);
	
			throw new \InvalidArgumentException("Index '$index' does not exists in property '$property'");
		}
		//try to convert value to string
		elseif (($value = $this->toString($value)) !== false) {
			//check string length against index
			if ($index < strlen($value))
				return $this->castParameter($value[$index], $type);
	
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
	 * @throws \OutOfBoundsException
	 * @return string
	 */
	protected function getSubIndex($value, $subindex = null, $type = null) {
		//if no subindex is specified just cast main argument
		if (is_null($subindex))
			return $this->castParameter($value, $type);
	
		//check if the value is null
		if (is_null($value))
			throw new \InvalidArgumentException("Trying to obtain a value from NULL parameter on index $index");
	
		//check whether this value is the first argument
		if (is_array($value) || is_object($value)) {
			$value = ArgumentWrapper::wrap($value);
			//check if the requested property exists
			if (!$value->offsetExists($subindex))
				throw new \InvalidArgumentException("Property '$subindex' not found on given parameter");
				
			if (is_null($type))
				$type = $value->getPropertyType($subindex);
				
			return $this->castParameter($value->offsetGet($subindex), $type);
		}
		elseif (($value = $this->toString($value)) !== false) {
			//check that subindex is numeric
			if (!is_numeric($subindex))
				throw new \InvalidArgumentException("Subindex '$subindex' is not a number");
				
			//convert subindex to a number
			$subindex = intval($subindex);		
			//check string length against index
			if ($subindex < strlen($value) && $subindex >= 0)
				return $this->castParameter($value[$subindex], $type);
	
			throw new \OutOfBoundsException("Index '$subindex' out of bounds for argument $index");
		}
	
		throw new \InvalidArgumentException("Cannot obtain index '$subindex' from a non-array type");
	}
	
	/**
	 * Obtains a range of elements from within an array/string
	 * @param mixed $property
	 * @param int $left_index
	 * @param int $right_index
	 * @param string $type
	 * @throws \InvalidArgumentException
	 * @return string
	 */
	protected function getRange($property, $left_index, $right_index, $type = null) {
		//verify valid property
		if (!$this->wrappedArg->offsetExists($property))
			throw new \InvalidArgumentException("Unknown property '$property'");
	
		//check if both indexes are empty
		if (empty($left_index) && strlen($right_index) == 0)
			return $this->castParameter($this->wrappedArg->offsetGet($property), $type);
	
		//check if the value is null
		$value = $this->wrappedArg->offsetGet($property);
		if (is_null($value))
			throw new \InvalidArgumentException("Trying to obtain value from NULL parameter on property '$property'");
	
		//check value type
		if (is_array($value)) {
			//get array slice
			$right_index = strlen($right_index) == 0 ? null : intval($right_index);
			return $this->castParameter(array_slice($value, intval($left_index), $right_index), $type);
		}
		//try to convert value to string
		elseif (($value = $this->toString($value)) !== false) {
			if (strlen($right_index) == 0)
				return $this->castParameter(substr($value, intval($left_index)), $type);
	
			return $this->castParameter(substr($value, intval($left_index), intval($right_index)), $type);
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
		if (empty($left_index) && strlen($right_index) == 0)
			return $this->castParameter($value, $type);
	
		//check if value is null
		if (is_null($value))
			throw new \InvalidArgumentException("Trying to obtain a value from NULL parameter on index $index");
	
		//check value type
		if (is_array($value) || $value instanceof \ArrayObject) {
			$right_index = strlen($right_index) == 0 ? null : intval($right_index);
			return $this->castParameter(array_slice($value, intval($left_index), $right_index), $type);
		}
		elseif (($value = $this->toString($value)) !== false) {
			if (strlen($right_index) == 0)
				return $this->castParameter(substr($value, intval($left_index)), $type);

			return $this->castParameter(substr($value, intval($left_index), intval($right_index)), $type);
		}
			
		throw new \InvalidArgumentException("Cannot obtain indexes from a non-array type");
	}
	
	/**
	 * Returns a string that replaces a configuration expression within a string
	 * @param array $matches
	 * @return string
	 */
	protected function replaceConfigExpression($matches) {
		$property = $matches[1];
		//check key existence
		if (!array_key_exists($property, $this->config))
			return '';
		//convert to string, if possible
		if (($str = $this->toString($this->config[$property])) === false)
			return '';
		return $str;
	}
	
	/**
	 * Returns a string that replaces a property expression
	 * @param array $matches
	 * @return string
	 */
	protected function replacePropertyExpression($matches) {
		//count available matches
		//this indicates the type of expression to replace
		$total_matches = count($matches);
		$type = $subindex = null;
	
		switch ($total_matches) {
			/*
			 * Property
			 */
			case 4: //#{PROPERTY@1[INDEX]@2?:TYPE@3}
				$type = substr($matches[3], 1);
			case 3: //#{PROPERTY@1[INDEX]@2}
				$subindex = empty($matches[2]) ? null : substr($matches[2], 1, -1);
			case 2: //#{PROPERTY@1}
				$key = $matches[1];

				//obtain type from annotation (when possible)
				if (is_null($type) && $this->wrappedArg instanceof ObjectArgumentWrapper) {
					if ($this->wrappedArg->getClassProfile()->isEntity()) {
						$property = $this->wrappedArg->getClassProfile()->getProperty($key);
						$type = $property->getType();
					}
				}
	
				return $this->getIndex($key, $subindex, $type);
				break;
	
			/*
			 * Interval
			 */
			case 9: //#{PROPERTY@4[LEFT_INDEX@6..RIGHT_INDEX@7]:TYPE@8}
				$type = substr($matches[8], 1);
			case 8: //#{PROPERTY@4[LEFT_INDEX@6..RIGHT_INDEX@7]}
				$key = $matches[4];
					
				//obtain type from annotation (when possible)
				if (is_null($type) && $this->wrappedArg instanceof ObjectArgumentWrapper) {
					if ($this->wrappedArg->getClassProfile()->isEntity()) {
						$property = $this->wrappedArg->getClassProfile()->getProperty($key);
						$type = $property->getType();
					}
				}
					
				return $this->getRange($key, $matches[6], $matches[7], $type);
				break;
		}
	}
	
	/**
	 * Returns a string that replaces an argument expression
	 * @param array$matches
	 * @throws \OutOfBoundsException
	 * @throws \InvalidArgumentException
	 * @return string
	 */
	protected function replaceArgumentExpression($matches) {
		//count available matches
		//this indicates the type of expression to replace
		$total_matches = count($matches);
		$type = $subindex = null;
	
		//%{TYPE|CLASS@1}
		if ($total_matches == 2) {
			//check if there are arguments left
			if ($this->counter >= count($this->args))
				throw new \OutOfBoundsException("No arguments left for expression '{$matches[0]}'");

			return $this->castParameter($this->args[$this->counter++], $matches[1]);
		}
	
		switch ($total_matches) {
			/*
			 * Simple index
			 */
			case 5: //%{NUMBER@2[INDEX]@3?:TYPE@4}
				$type = substr($matches[4], 1);
			case 4: //%{NUMBER@2[INDEX]@3}
				$subindex = empty($matches[3]) ? null : substr($matches[3], 1, -1);
			case 3: //%{NUMBER@2}
				$index = intval($matches[2]);
					
				if (!array_key_exists($index, $this->args))
					throw new \InvalidArgumentException("No value found on index $index");
	
				if ($index == 0 && !is_null($subindex))
					return $this->getIndex($subindex, null, $type);
				
				return $this->getSubIndex($this->args[$index], $subindex, $type);
	
			/*
			 * Interval
			 */
			case 10: //%{NUMBER@5[LEFT@7?..RIGHT@8?]:TYPE@9}
				$type = substr($matches[9], 1);
			case 9: //%{NUMBER@5[LEFT@7?..RIGHT@8?]}
				$index = intval($matches[5]);
					
				if (!array_key_exists($index, $this->args))
					throw new \InvalidArgumentException("No value found on index $index");
					
				return $this->getSubRange($this->args[$index], $matches[7], $matches[8], $type);
		}
	}
	
	/**
	 * Parses a string and replaces all references to arguments
	 * @param string $expr
	 * @param array $args
	 * @param array $config
	 * @throws \RuntimeException
	 * @throws \InvalidArgumentException
	 * @throws \OutOfBoundsException
	 * @return string
	 */
	public function format($expr, $args, $config) {
		//store arguments
		$this->args = $args;
	
		//store configuration
		$this->config = $config;
	
		//initialize counter
		$this->counter = 0;
	
		//replace configuration propeties expressions
		if (preg_match(self::CONFIG_REGEX, $expr))
			$expr = preg_replace_callback(self::CONFIG_REGEX, [$this, 'replaceConfigExpression'], $expr);
	
		//replace database prefix (short form)
		$expr = str_replace(self::SHORT_PREFIX, array_key_exists('db.prefix', $config) ? $config['db.prefix'] : '', $expr);
	
		//wrap argument (if any)
		if (array_key_exists(0, $args) && (is_object($args[0]) || is_array($args[0])))
			$this->wrappedArg = ArgumentWrapper::wrap($args[0]);
	
		//replace properties expressions
		if (preg_match(self::PROPERTY_PARAM_REGEX, $expr)) {
			//validate argument
			if (empty($args[0]))
				throw new \InvalidArgumentException("No valid parameters have been defined for this query");
			elseif (!is_object($args[0]) && !is_array($args[0]))
				throw new \InvalidArgumentException("Specified parameter is not an array/object");
				
			//move default counter to 1
			$this->counter = 1;
			$expr = preg_replace_callback(self::PROPERTY_PARAM_REGEX, [$this, 'replacePropertyExpression'], $expr);
		}
	
		//replace inline parameters
		if (!empty($args) && preg_match(self::INLINE_PARAM_REGEX, $expr))
			$expr = preg_replace_callback(self::INLINE_PARAM_REGEX, [$this, 'replaceArgumentExpression'], $expr);

		return $expr;
	}
}