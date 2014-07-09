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
	
	/**
	 * Primary key property
	 * @var string
	 */
	public $primaryKey;
	
	public function __construct($classname) {
		//store class annotations
		$this->reflectionClass = new \ReflectionClass($classname);
		$this->classAnnotations = Facade::getAnnotations($this->reflectionClass);
		
		//get properties annotations
		$propertyList = $this->reflectionClass->getProperties();
		$this->propertiesAnnotations = [];
		
		foreach ($propertyList as $reflectionProperty) {
			$this->propertiesAnnotations[$reflectionProperty->getName()] = Facade::getAnnotations($reflectionProperty);
		}
		
		//store properties metadata
		$this->dynamicAttributes = $this->propertiesConfig = [];
			
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
				
				//determine if the current property is primary key
				if ($this->propertiesConfig[$name]->isPrimaryKey && is_null($this->primaryKey)) {
					$this->primaryKey = $name;
				}
			}
		}
	}
	
	public function isEntity() {
		return $this->classAnnotations->has('map.entity');
	}
	
	public function isUnquoted() {
		return $this->classAnnotations->has('map.unquoted');
	}
	
	public function getReferencedTable() {
		if ($this->classAnnotations->has('map.table')) {
			return $this->classAnnotations->get('map.table');
		}
		
		return strtolower($this->reflectionClass->getShortName());
	}
	
	public function getNamespace() {
		if ($this->classAnnotations->has('map.namespace')) {
			return $this->classAnnotations->get('map.namespace');
		}
		
		return null;
	}
	
	public function getFieldNames() {
		$fields = [];
		
		foreach ($this->propertiesConfig as $name => $property) {
			if (isset($property->column)) {
				$fields[$name] = $property->column;
			}
			else {
				$fields[$name] = $name;
			}
		}
		
		return $fields;
	}
}
?>