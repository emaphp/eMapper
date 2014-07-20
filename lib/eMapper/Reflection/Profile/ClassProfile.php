<?php
namespace eMapper\Reflection\Profile;

use eMapper\Result\Relation\MacroExpression;
use eMapper\Result\Relation\StatementCallback;
use eMapper\Result\Relation\QueryCallback;
use eMapper\Result\Relation\StoredProcedureCallback;
use Minime\Annotations\Facade;

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
	
	/**
	 * Entity field names
	 * @var array
	 */
	public $fieldNames;
	
	/**
	 * Column names
	 * @var array
	 */
	public $columnNames;
	
	public function __construct($classname) {
		//store class annotations
		$this->reflectionClass = new \ReflectionClass($classname);
		$this->classAnnotations = Facade::getAnnotations($this->reflectionClass);
		
		//get properties annotations
		$this->propertiesConfig = $this->dynamicAttributes = [];
		$propertyList = $this->reflectionClass->getProperties();
		
		foreach ($propertyList as $reflectionProperty) {
			//get property annotations
			$annotations = Facade::getAnnotations($reflectionProperty);
			
			//get property name for indexation
			$propertyName = $reflectionProperty->getName();
			
			//determiny property type
			if ($annotations->has('Eval')) {
				$this->dynamicAttributes[$propertyName] = new MacroExpression($propertyName, $annotations, $reflectionProperty);
			}
			elseif ($annotations->has('StatementId')) {
				$this->dynamicAttributes[$propertyName] = new StatementCallback($propertyName, $annotations, $reflectionProperty);
			}
			elseif ($annotations->has('Query')) {
				$this->dynamicAttributes[$propertyName] = new QueryCallback($propertyName, $annotations, $reflectionProperty);
			}
			elseif ($annotations->has('Procedure')) {
				$this->dynamicAttributes[$propertyName] = new StoredProcedureCallback($propertyName, $annotations, $reflectionProperty);
			}
			else {
				$this->propertiesConfig[$propertyName] = new PropertyProfile($propertyName, $annotations, $reflectionProperty);
				
				if ($this->propertiesConfig[$propertyName]->isPrimaryKey) {
					$this->primaryKey = $propertyName;
				}
			}
		}
		
		$this->fieldNames = [];
		
		//build field list
		foreach ($this->propertiesConfig as $name => $property) {
			if (isset($property->column)) {
				$this->fieldNames[$name] = $property->column;
			}
			else {
				$this->fieldNames[$name] = $name;
			}
		}
		
		$this->columnNames = array_flip($this->fieldNames);
	}
	
	public function isEntity() {
		return $this->classAnnotations->has('Entity');
	}
	
	public function isResultMap() {
		return $this->classAnnotations->has('ResultMap');
	}
	
	public function isParameterMap() {
		return $this->classAnnotations->has('ParameterMap');
	}
	
	public function isTypeHandler() {
		return $this->classAnnotations->has('TypeHandler');
	}
	
	public function isSafe() {
		if ($this->classAnnotations->has('Safe')) {
			return (bool) $this->classAnnotations->get('Safe');
		}
		
		return false;
	}
	
	public function getReferredTable() {
		if ($this->classAnnotations->has('Entity')) {
			return $this->classAnnotations->get('Entity');
		}
		
		//return default table
		return strtolower($this->reflectionClass->getShortName()) . 's';
	}
	
	public function getNamespace() {
		if ($this->classAnnotations->has('DefaultNamespace')) {
			return $this->classAnnotations->get('DefaultNamespace');
		}
		
		return null;
	}
	
	public function getFieldType($field) {
		if (array_key_exists($field, $this->fieldNames)) {
			return $this->propertiesConfig[$field]->type;
		}
	}
	
	public function getColumnType($column) {		
		if (array_key_exists($column, $this->columnNames)) {
			$attr = $this->columnNames[$column];
			return $this->propertiesConfig[$attr]->type;
		}
	}
	
	public function getReflectionProperty($property) {
		return $this->entity->propertiesConfig[$primaryKey]->reflectionProperty;
	}
}
?>