<?php
namespace eMapper\Engine\Generic\Statement;

use eMapper\Cache\Key\CacheKey;
use eMapper\Dynamic\Builder\EnvironmentBuilder;
use eMapper\Reflection\Parameter\ParameterWrapper;
use eMapper\Reflection\Profiler;
use eMapper\Type\TypeHandler;
use eMacros\Program\SimpleProgram;

/**
 * The GenericStatement class is responsible for generating a sql query which is then sent to the database. 
 * @author emaphp
 */
abstract class GenericStatement extends CacheKey {
	use EnvironmentBuilder;

	//Ex: [? (null? (#order)) ?] [?int (#limit) ?]
	const DYNAMIC_SQL_REGEX = '/(?>\\[\?)([A-z]{1}[\w|\\\\]*)?\s+(.+?)\?\]/';
	
	protected function castArray($value, TypeHandler $typeHandler, $join_string = ',') {
		$list = [];
	
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
				elseif (!Profiler::getClassProfile(get_class($typeHandler))->isSafe()) {
					$new_elem = "'" . $this->escapeString($val) . "'";
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
	
			if ($typeHandler === false) {
				throw new \RuntimeException("No type handler found for type '$type'");
			}
			
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
			$value = strval($value);
		}
		//escape string if necessary
		elseif (!Profiler::getClassProfile(get_class($typeHandler))->isSafe()) {
			$value = "'" . $this->escapeString($value) . "'";
		}
	
		return $value;
	}
	
	/**
	 * Executes a captured string as a program
	 * @param Environment $env
	 * @param string $expr
	 * @return mixed
	 */
	protected function executeDynamicSQL($env, $expr) {	
		//run program
		$program = new SimpleProgram($expr);
		return $program->executeWith($env, $this->args);
	}
	
	/**
	 * Replaces a dynamic sql expression
	 * @param array $matches
	 */
	protected function replaceDynamicExpression($matches) {
		//run program
		$program = new SimpleProgram($matches[2]);
		$value = $program->executeWith($this->buildEnvironment($this->config), $this->args);
		
		//cast return type to the specified type (if any)
		if (!empty($matches[1])) {
			return $this->castParameter($value, $matches[1]);
		}
		
		//return unsafe string
		return $this->toString($value);
	}
	
	/**
	 * Builds a query with the given arguments
	 * (non-PHPdoc)
	 * @see \eMapper\Cache\Key\CacheKey::build()
	 */
	public function build($expr, $args, $config) {
		//store arguments
		$this->args = $args;
		
		//store configuration
		$this->config = $config;
		
		//initialize counter
		$this->counter = 0;
						
		//replace dynamic sql expressions
		if (preg_match(self::DYNAMIC_SQL_REGEX, $expr)) {
			$expr = preg_replace_callback(self::DYNAMIC_SQL_REGEX, [$this, 'replaceDynamicExpression'], $expr);
		}
		
		//replace configuration propeties expressions
		if (preg_match(self::CONFIG_REGEX, $expr)) {
			$expr = preg_replace_callback(self::CONFIG_REGEX, [$this, 'replaceConfigExpression'], $expr);
		}
		
		//replace database prefix (short form)
		$expr = str_replace(self::SHORT_PREFIX, array_key_exists('db.prefix', $config) ? strval($config['db.prefix']) : '', $expr);
		
		//replace properties expressions
		if (preg_match(self::PROPERTY_PARAM_REGEX, $expr)) {
			//validate argument
			if (empty($args[0])) {
				throw new \InvalidArgumentException("No valid parameters have been defined for this query");
			}
			elseif (!is_object($args[0]) && !is_array($args[0])) {
				throw new \InvalidArgumentException("Specified parameter is not an array/object");
			}
		
			//move default counter to 1
			$this->counter = 1;
			
			//wrap first argument
			$this->wrappedArg = ParameterWrapper::wrapValue($args[0], $this->parameterMap);
			$expr = preg_replace_callback(self::PROPERTY_PARAM_REGEX, [$this, 'replacePropertyExpression'], $expr);
		}
		
		//replace inline parameters
		if (!empty($args) && preg_match(self::INLINE_PARAM_REGEX, $expr)) {
			$expr = preg_replace_callback(self::INLINE_PARAM_REGEX, [$this, 'replaceArgumentExpression'], $expr);
		}
		
		return $expr;
	}
	
	/**
	 * Escapes a string for the current database engine
	 * @param string $string
	 */
	public abstract function escapeString($string);
}
?>