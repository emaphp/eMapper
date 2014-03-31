<?php
namespace eMapper\Reflection\Profile;

use eMapper\Result\Relation\MacroExpression;
use eMapper\Result\Relation\StatementCallback;
use eMapper\Result\Relation\QueryCallback;
use eMapper\Result\Relation\StoredProcedureCallback;
use eMapper\Reflection\Facade;

class ClassProfile {
	/**
	 * Reflection class
	 * @var \ReflectionClass
	 */
	public $reflectionClass;
	
	/**
	 * Class annotations
	 * @var AnnotationsBag
	 */
	public $classAnnotations;
	
	/**
	 * Property annotations
	 * @var array
	 */
	public $propertiesAnnotations;
	
	/**
	 * Dynamic attributes
	 * @var array
	 */
	public $dynamicAttributes;
	
	/**
	 * Properties configuration
	 * @var array
	 */
	public $propertiesConfig;
	
	public function __construct($classname) {
		$this->reflectionClass = new \ReflectionClass($classname);
		$this->classAnnotations = Facade::getAnnotations($this->reflectionClass);
		
		$propertyList = $this->reflectionClass->getProperties();
		$this->propertiesAnnotations = array();
		
		foreach ($propertyList as $reflectionProperty) {
			$this->propertiesAnnotations[$reflectionProperty->getName()] = Facade::getAnnotations($reflectionProperty);
		}
		
		$this->dynamicAttributes = $this->propertiesConfig = array();
			
		foreach ($this->propertiesAnnotations as $name => $attr) {
			if ($attr->has('map.eval')) {
				$this->dynamicAttributes[$name] = new MacroExpression($name, $attr, $this->reflectionClass->getProperty($name));
			}
			elseif ($attr->has('map.stmt')) {
				$this->dynamicAttributes[$name] = new StatementCallback($name, $attr, $this->reflectionClass->getProperty($name));
			}
			elseif ($attr->has('map.query')) {
				$this->dynamicAttributes[$name] = new QueryCallback($name, $attr, $this->reflectionClass->getProperty($name));
			}
			elseif ($attr->has('map.procedure')) {
				$this->dynamicAttributes[$name] = new StoredProcedureCallback($name, $attr, $this->reflectionClass->getProperty($name), $classname);
			}
			else {
				$this->propertiesConfig[$name] = new PropertyProfile($name, $attr, $this->reflectionClass->getProperty($name));
			}
		}
	}
	
	public function isEntity() {
		return $this->classAnnotations->has('map.entity');
	}
	
	public function isUnquoted() {
		return $this->classAnnotations->has('map.unquoted');
	}
}
?>