<?php
namespace eMapper\Environment\Provider;

abstract class EnvironmentProvider {
	/**
	 * Environment list
	 * @var array
	 */
	public static $environments = array();
	
	/**
	 * Obtains a dynamic SQL environment instance
	 * @param string $classname
	 * @throws \InvalidArgumentException
	 */
	public static function getEnvironment($classname) {
		if (!array_key_exists($classname, self::$environments)) {
			if (!is_string($classname) || empty($classname)) {
				throw new \InvalidArgumentException("Parameter is not a valid environment class");
			}
			elseif (!class_exists($classname, true)) {
				throw new \InvalidArgumentException("Class $classname was not found");
			}
			
			$rc = new \ReflectionClass($classname);
			
			if (!$rc->isSubclassOf('eMacros\Environment\Environment')) {
				throw new \InvalidArgumentException("Class $classname is not a valid environment class");
			}
			
			self::$environments[$classname] = new $classname();
		}
		
		return self::$environments[$classname];
	}
}
?>