<?php
namespace eMapper\Reflection;

use Minime\Annotations\Facade;
use Minime\Annotations\AnnotationsBag;

abstract class Profiler extends Facade {
	/**
	 * Class profiles
	 * @var array
	 */
	public static $profiles = array();
	
	/**
	 * Builds and stores a class annotation profile
	 * @param string $classname
	 */
	protected static function buildProfile($classname) {
		//get reflection class
		$reflectionClass = new \ReflectionClass($classname);
		$classAnnotations = self::getAnnotations($reflectionClass);
		
		//parse properties
		$properties = array();
		$reflectionProperties = $reflectionClass->getProperties();
		
		foreach ($reflectionProperties as $reflectionProperty) {
			$properties[$reflectionProperty->getName()] = self::getAnnotations($reflectionProperty);
		}
		
		self::$profiles[$classname] = array($classAnnotations, $properties, $reflectionClass);
	}
	
	/**
	 * Obtains an array containing all class annotations
	 * @param string $classname
	 * @return array
	 */
	public static function getClassProfile($classname) {
		if (!array_key_exists($classname, self::$profiles)) {
			self::buildProfile($classname);
		}
		
		return self::$profiles[$classname];
	}
	
	/**
	 * Obtains an annotation bag containing all annotations defined for the given class
	 * @param string $classname
	 * @return AnnotationsBag
	 */
	public static function getClassAnnotations($classname) {
		if (!array_key_exists($classname, self::$profiles)) {
			self::buildProfile($classname);
		}
		
		return self::$profiles[$classname][0];
	}
	
	/**
	 * Obtains an array with all annotation bags by property name
	 * @param string $classname
	 * @return array
	 */
	public static function getClassProperties($classname) {
		if (!array_key_exists($classname, self::$profiles)) {
			self::buildProfile($classname);
		}
		
		return self::$profiles[$classname][1];
	} 
	
	/**
	 * Obtains a reflection class
	 * @return \ReflectionClass
	 */
	public static function getReflectionClass($classname) {
		if (!array_key_exists($classname, self::$profiles)) {
			self::buildProfile($classname);
		}
		
		return self::$profiles[$classname][2];
	}
	
	/**
	 * Determines if the given class has benn declared as an entity
	 * @param string $classname
	 */
	public static function isEntity($classname) {
		if (!array_key_exists($classname, self::$profiles)) {
			self::buildProfile($classname);
		}
		
		return self::$profiles[$classname][0]->has('entity');
	}
}
?>