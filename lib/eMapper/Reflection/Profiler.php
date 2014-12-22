<?php
namespace eMapper\Reflection;

/**
 * The Profiler class is a multiton object that stores class profiles.
 * @author emaphp
 */
abstract class Profiler {
	/**
	 * Class profiles
	 * @var array:\eMapper\Reflection\ClassProfile
	 */
	protected static $profiles = [];
	
	/**
	 * Obtains a ClassProfile instance for the given classname
	 * @param string $classname
	 * @return \eMapper\Reflection\ClassProfile
	 */
	public static function getClassProfile($classname) {
		if (!array_key_exists($classname, self::$profiles))
			self::$profiles[$classname] = new ClassProfile($classname);
		
		return self::$profiles[$classname];
	}
}