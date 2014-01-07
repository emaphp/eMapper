<?php
namespace eMapper\Engine\MySQL\Statement;

use eMapper\Cache\Key\CacheKey;
use eMapper\Type\TypeManager;
use eMapper\Type\TypeHandler;
use eMapper\Reflection\Profiler;
use eMacros\Environment\Environment;
use eMacros\Program\SimpleProgram;
use eMapper\Environment\Provider\EnvironmentProvider;

class MySQLStatement extends CacheKey {
	//Ex: [[ (null? (@order)) ]]
	const DYNAMIC_SQL_REGEX = '/@\[\[(.*)\]\]@/';
	
	/**
	 * MySQL connection
	 * @var mysqli
	 */
	protected $conn;

	public function __construct($conn, TypeManager $typeManager) {
		parent::__construct($typeManager);
		$this->conn = $conn;
	}
	
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
				else {
					$profile = Profiler::getClassAnnotations(get_class($typeHandler), false);
					
					if (!$profile->has('unquoted') && $escape_string) {
						$new_elem = "'" . $this->conn->real_escape_string($val) . "'";
					}
				}
			}
				
			//verify if that element already exists
			if (!in_array($new_elem, $list, true)) {
				$list[] = $new_elem;
			}
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
			$profile = Profiler::getClassAnnotations(get_class($typeHandler));
			
			if (!$profile->has('unquoted')) {
				$value = "'" . $this->conn->real_escape_string($value) . "'";
			}
		}
		
		return $value;
	}
	
	public function build($expr, $args, $config) {
		//replace dynamic sql expressions
		if (preg_match(self::DYNAMIC_SQL_REGEX, $expr)) {
			$expr = preg_replace_callback(self::DYNAMIC_SQL_REGEX,
					function ($matches) use ($config) {
						$env = EnvironmentProvider::getEnvironment($config['dynamic.environment']);
						$programClass = $config['dynamic.program'];
						$program = new $programClass($matches[1]);
						return $program->execute($env);
					},
					$expr);
		}
		
		return parent::build($expr, $args, $config);
	}
}