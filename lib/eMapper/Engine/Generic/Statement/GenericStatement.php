<?php
namespace eMapper\Engine\Generic\Statement;

use eMapper\Cache\Key\CacheKey;
use eMapper\Dynamic\Builder\EnvironmentBuilder;
use eMapper\Reflection\Parameter\ParameterWrapper;
use eMapper\Dynamic\Provider\EnvironmentProvider;
use eMapper\Reflection\Profiler;
use eMapper\Type\TypeHandler;
use eMacros\Program\SimpleProgram;

abstract class GenericStatement extends CacheKey {
	use EnvironmentBuilder;
	
	//Ex: [[ (null? (#order)) ]]
	const UNESCAPED_DYNAMIC_SQL_REGEX = '/\[\[(.+?)\]\]/';
	
	//Ex: {{ (null? (#order)) }} {{:int (#limit)) }}
	const DYNAMIC_SQL_REGEX = '/\{\{(?::([\w|\\\\]+)\s+)?(.+?)\}\}/';
	
	/**
	 * Current mapper configuration
	 * @var array
	 */
	public $config;
	
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
					$new_elem = strval($new_elem);
				}
				else {
					if (!Profiler::getClassProfile(get_class($typeHandler))->isUnquoted()) {
						$new_elem = "'" . $this->escapeString($val) . "'";
					}
				}
			}
	
			$list[] = $new_elem;
		}
	
		//return joined expression
		return implode($join_string, $list);
	}
	
	protected function castParameter($value, $type = null) {
		//check null value
		if (is_null($value)) {
			return 'NULL';
		}
		elseif (is_null($type)) {
			//obtain default type handler
			$typeHandler = $this->getDefaultTypeHandler($value);
	
			//a null type handler equals an empty list
			if (is_null($typeHandler)) {
				if (is_array($value) && count($value) > 0) {
					return 'NULL';
				}
	
				return '';
			}
	
			if (is_array($value)) {
				return $this->castArray($value, $typeHandler);
			}
		}
		else {
			//cast value to the specified type
			$typeHandler = $this->typeManager->getTypeHandler($type);
	
			if (is_array($value)) {
				return $this->castArray($value, $typeHandler);
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
		//escape string if necessary
		else {
			if (!Profiler::getClassProfile(get_class($typeHandler))->isUnquoted()) {
				$value = "'" . $this->escapeString($value) . "'";
			}
		}
	
		return $value;
	}
	
	protected function executeDynamicSQL($env, $expr) {	
		//run program
		$program = new SimpleProgram($expr);
		return $program->executeWith($env, $this->args);
	}
	
	public function build($expr, $args, $config, $parameterMap = null) {
		$this->args = $args;
		$this->config = $config;
		
		//wrap first parameter
		if (isset($this->args[0]) && (is_object($args[0]) || is_array($args[0]))) {
			$this->wrappedArg = ParameterWrapper::wrap($args[0], $parameterMap);
		}
		
		//replace dynamic sql expressions (unescaped)
		if (preg_match(self::UNESCAPED_DYNAMIC_SQL_REGEX, $expr)) {
			//set environment config
			$env = $this->buildEnvironment($config);
			
			$expr = preg_replace_callback(self::UNESCAPED_DYNAMIC_SQL_REGEX,
					function ($matches) use ($env) {
						return $this->toString($this->executeDynamicSQL($env, $matches[1]));
					},
					$expr);
		}
		
		//replace dynamic sql expressions
		if (preg_match(self::DYNAMIC_SQL_REGEX, $expr)) {
			//set environment config
			$env = $this->buildEnvironment($config);
			
			$expr = preg_replace_callback(self::DYNAMIC_SQL_REGEX,
					function ($matches) use ($env) {
						$value = $this->executeDynamicSQL($env, $matches[2]);
						$type = !empty($matches[1]) ? $matches[1] : 'string';
						return $this->castParameter($value, $type);
					},
					$expr);
		}
		
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
		
		//replace database prefix (short form)
		if (array_key_exists('db.prefix', $config)) {
			$expr = str_replace(self::SHORT_PREFIX, $config['db.prefix'], $expr);
		}
		
		if (preg_match(self::PROPERTY_PARAM_REGEX, $expr)) {
			//validate argument
			if (empty($args[0])) {
				throw new \InvalidArgumentException("No valid parameters have been defined for this query");
			}
			elseif (!is_object($args[0]) && !is_array($args[0])) {
				throw new \InvalidArgumentException("Specified parameter is not an array/object");
			}
		
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
									$type = $this->getDefaultType($this->wrappedArg, $key);
								}
		
								return $this->getIndex($this->wrappedArg, $key, $subindex, $type);
								break;
									
								/**
								 * Interval
								 */
							case 9: //#{PROPERTY@4[LEFT_INDEX@6..RIGHT_INDEX@7]:TYPE@8}
								$type = substr($matches[8], 1);
							case 8: //#{PROPERTY@4[LEFT_INDEX@6..RIGHT_INDEX@7]}
								$key = $matches[4];
		
								if (is_null($type) && isset($this->parameterMap)) {
									$type = $this->getDefaultType($this->wrappedArg, $key);
								}
		
								return $this->getRange($this->wrappedArg, $key, $matches[6], $matches[7], $type);
								break;
						}
		
					}, $expr);
		}
		
		//replace inline parameter
		if (!empty($args) && preg_match(self::INLINE_PARAM_REGEX, $expr)) {
			$this->args[0] = $args[0];
			$total_args = count($this->args);
			$expr = preg_replace_callback(self::INLINE_PARAM_REGEX,
					function ($matches) use ($total_args) {
						static $n = 0;
						$total_matches = count($matches);
		
						if ($total_matches == 2) { //%{TYPE@1 | CLASS@1}
							//check if there is arguments left
							if ($n >= $total_args) {
								throw new \OutOfBoundsException("No arguments left for expression '{$matches[0]}'");
							}

							return $this->castParameter($this->args[$n++], $matches[1]);
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
	
	public abstract function escapeString($string);
}
?>