<?php
namespace eMapper\Reflection;

use eMapper\Reflection\Profile\ClassProfile;

abstract class Profiler {
	/**
	 * Class profiles
	 * @var array
	 */
	public static $profiles = array();
	
	/**
	 * Obtains a ClassProfile instance for the given classname
	 * @param string $classname
	 * @return ClassProfile
	 */
	public static function getClassProfile($classname) {
		if (!array_key_exists($classname, self::$profiles)) {
			self::$profiles[$classname] = new ClassProfile($classname);
		}
		
		return self::$profiles[$classname];
	}
}
?>