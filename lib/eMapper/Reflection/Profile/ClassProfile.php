<?php
namespace eMapper\Reflection\Profile;

use eMapper\Reflection\Profile\Dynamic\MacroExpression;
use eMapper\Reflection\Profile\Dynamic\StatementCallback;
use eMapper\Reflection\Profile\Dynamic\QueryCallback;
use eMapper\Reflection\Profile\Dynamic\StoredProcedureCallback;
use eMapper\Annotations\Facade;
use eMapper\Reflection\Profile\Association\OneToOne;
use eMapper\Reflection\Profile\Association\OneToMany;
use eMapper\Reflection\Profile\Association\ManyToOne;
use eMapper\Reflection\Profile\Association\ManyToMany;

/**
 * The ClassProfile class provides details of the implementation and metadata available in a class.
 * @author emaphp
 */
class ClassProfile {
	/**
	 * Reflection class
	 * @var \ReflectionClass
	 */
	private $reflectionClass;
	
	/**
	 * Class annotations
	 * @var AnnotationsBag
	 */
	private $classAnnotations;
	
	/**
	 * Property profiles
	 * @var array
	 */
	private $properties = [];
	
	/**
	 * Property names as an associative array PROPERTY => COLUMN 
	 * @var array
	 */
	private $propertyNames = [];
	
	/**
	 * Column names as an associative array COLUMN => PROPERTY
	 * @var array
	 */
	private $columnNames = [];
	
	/**
	 * First order attributes: Macros and Scalars
	 * @var array
	 */
	private $firstOrderAttributes = [];
	
	/**
	 * Second order attributes: Queries, Statements, Procedure calls
	 * @var array
	 */
	private $secondOrderAttributes = [];
	
	/**
	 * Primary key property
	 * @var string
	 */
	private $primaryKey;
	
	/**
	 * Class associations
	 * @var array
	 */
	private $associations = [];
	
	/**
	 * Foreign keys
	 * @var array
	 */
	private $foreignKeys = [];
	
	public function __construct($classname) {
		//store class annotations
		$this->reflectionClass = new \ReflectionClass($classname);
		$this->classAnnotations = Facade::getAnnotations($this->reflectionClass);
		
		//get properties annotations
		$propertyList = $this->reflectionClass->getProperties();
		
		foreach ($propertyList as $reflectionProperty) {
			//get property annotations
			$annotations = Facade::getAnnotations($reflectionProperty);
			
			$isScalar = $annotations->has('Scalar');
			
			//get property name for indexation
			$propertyName = $reflectionProperty->getName();
			
			//determiny property type
			if ($annotations->has('Eval')) {
				$this->firstOrderAttributes[$propertyName] = new MacroExpression($propertyName, $annotations, $reflectionProperty);
			}
			elseif ($annotations->has('StatementId')) {				
				if ($isScalar) {
					$this->firstOrderAttributes[$propertyName] = new StatementCallback($propertyName, $annotations, $reflectionProperty);
				}
				else {
					$this->secondOrderAttributes[$propertyName] = new StatementCallback($propertyName, $annotations, $reflectionProperty);
				}
			}
			elseif ($annotations->has('Query')) {
				if ($isScalar) {
					$this->firstOrderAttributes[$propertyName] = new QueryCallback($propertyName, $annotations, $reflectionProperty);
				}
				else {
					$this->secondOrderAttributes[$propertyName] = new QueryCallback($propertyName, $annotations, $reflectionProperty);
				}
			}
			elseif ($annotations->has('Procedure')) {
				if ($isScalar) {
					$this->firstOrderAttributes[$propertyName] = new StoredProcedureCallback($propertyName, $annotations, $reflectionProperty);
				}
				else {
					$this->secondOrderAttributes[$propertyName] = new StoredProcedureCallback($propertyName, $annotations, $reflectionProperty);
				}
			}
			elseif ($annotations->has('OneToOne')) {
				$assoc = new OneToOne($propertyName, $annotations, $reflectionProperty);
				$this->associations[$propertyName] = $assoc;
				
				if ($assoc->isForeignKey()) {
					$attr = $assoc->getAttribute()->getArgument();
					$this->foreignKeys[$attr] = $propertyName;
				}
			}
			elseif ($annotations->has('OneToMany')) {
				$this->associations[$propertyName] = new OneToMany($propertyName, $annotations, $reflectionProperty);
			}
			elseif ($annotations->has('ManyToOne')) {
				$assoc = new ManyToOne($propertyName, $annotations, $reflectionProperty);
				$this->associations[$propertyName] = $assoc;
				
				//add foreign key
				$attr = $assoc->getAttribute()->getArgument();
				$this->foreignKeys[$attr] = $propertyName;
			}
			elseif ($annotations->has('ManyToMany')) {
				$this->associations[$propertyName] = new ManyToMany($propertyName, $annotations, $reflectionProperty);
			}
			else {
				$this->properties[$propertyName] = new PropertyProfile($propertyName, $annotations, $reflectionProperty);
				
				//check if property is declared as primary key
				if ($this->properties[$propertyName]->isPrimaryKey()) {
					$this->primaryKey = $propertyName;
				}
			}
		}
		
		foreach ($this->properties as $propertyProfile) {
			if (!$propertyProfile->isReadOnly()) {
				$this->propertyNames[$propertyProfile->getName()] = $propertyProfile->getColumn();
			}
		}
		
		$this->columnNames = array_flip($this->propertyNames);
		
		//validate entity
		if ($this->isEntity() ) {
			$referredTable = $this->getReferredTable();
			
			if (empty($referredTable) || $referredTable === true) {
				throw new \RuntimeException(sprintf("Entity class %s must declare a table name", $this->reflectionClass->getName()));
			}
			
			if (is_null($this->primaryKey)) {
				throw new \RuntimeException(sprintf("Entity class %s must define a primary key attribute", $this->reflectionClass->getName()));
			}
		}
	}
	
	/**
	 * Obtains reflection class
	 * @return ReflectionClass
	 */
	public function getReflectionClass() {
		return $this->reflectionClass;
	}
	
	/**
	 * Return current class annotations
	 * @return AnnotationsBag
	 */
	public function getClassAnnotations() {
		return $this->classAnnotations;
	}
	
	/**
	 * Obtains class properties
	 * @return array
	 */
	public function getProperties() {
		return $this->properties;
	}
	
	/**
	 * Determines if current class has the given property
	 * @param string $property
	 * @return boolean
	 */
	public function hasProperty($property) {
		return array_key_exists($property, $this->properties);
	}
	
	/**
	 * Obtains the property profile with the given name (false if not found)
	 * @param string $property
	 * @return PropertyProfile|boolean
	 */
	public function getProperty($property) {
		if (!$this->hasProperty($property)) {
			return false;
		}
		
		return $this->properties[$property];
	}
	
	/**
	 * Obtains a property by its referred column
	 * @param string $column
	 * @return boolean|PropertyProfile
	 */
	public function getPropertyByColumn($column) {
		if (!array_key_exists($column, $this->columnNames)) {
			return false;
		}
		
		$property = $this->columnNames[$column];
		return $this->getProperty($property);
	}
	
	/**
	 * Obtains the class property names as an associative array (PROPERTY => COLUMN)
	 * @return array
	 */
	public function getPropertyNames() {
		return $this->propertyNames;
	}
	
	/**
	 * Obtains the columns referred by class properties as an associative array (COLUMN => PROPERTY)
	 * @return array
	 */
	public function getColumnNames() {
		return $this->columnNames;
	}
	
	/**
	 * Obtains class first order attributes
	 * @return array
	 */
	public function getFirstOrderAttributes() {
		return $this->firstOrderAttributes;
	}
	
	/**
	 * Obtains class second order attributes
	 * @return array
	 */
	public function getSecondOrderAttributes() {
		return $this->secondOrderAttributes;
	}
	
	/**
	 * Obtains the entity primary key property
	 * @param boolean $as_column
	 * @return string|NULL
	 */
	public function getPrimaryKey($as_column = false) {
		if ($as_column) {
			return $this->propertyNames[$this->primaryKey];
		}
		
		return $this->primaryKey;
	}
	
	/**
	 * Obtains the entity primary key property
	 * @return PropertyProfile
	 */
	public function getPrimaryKeyProperty() {
		$this->properties[$this->primaryKey];
	}
	
	/**
	 * Determines if the current class is an entity
	 * @return boolean
	 */
	public function isEntity() {
		return $this->classAnnotations->has('Entity');
	}
	
	/**
	 * Determines if the current class is a result map
	 * @return boolean
	 */
	public function isResultMap() {
		return $this->classAnnotations->has('ResultMap');
	}
	
	/**
	 * Determines if the current class is a parameter map
	 * @return boolean
	 */
	public function isParameterMap() {
		return $this->classAnnotations->has('ParameterMap');
	}
	
	/**
	 * Determines if the current class is a type handler
	 * @return boolean
	 */
	public function isTypeHandler() {
		return $this->reflectionClass->isSubclassOf('eMapper\Type\TypeHandler');
	}
	
	/**
	 * Determines if the current class return a safe value (type handlers only)
	 * @return boolean
	 */
	public function isSafe() {
		if ($this->classAnnotations->has('Safe')) {
			return (bool) $this->classAnnotations->get('Safe')->getValue();
		}
		
		return false;
	}
	
	/**
	 * Obtains the table referenced by current class (entities only)
	 * @return string
	 */
	public function getReferredTable() {
		return $this->classAnnotations->get('Entity')->getValue();
	}
	
	/**
	 * Obtains the default namespace of current class (entities only)
	 * @return string
	 */
	public function getNamespace() {
		$referredTable = $this->getReferredTable();
		
		if ($this->classAnnotations->has('DefaultNamespace')) {
			$ns = $this->classAnnotations->get('DefaultNamespace')->getValue();
			
			if (empty($ns) || $ns === true) {
				return $referredTable;
			}
			
			return $ns;
		}
		
		return $referredTable;
	}
	
	public function getAssociations() {
		return $this->associations;
	}
	
	public function getAssociation($name) {
		if (array_key_exists($name, $this->associations)) {
			return $this->associations[$name];
		}
		
		return false;
	}
	
	public function hasForeignKeys() {
		return !empty($this->foreignKeys);
	}
	
	public function getForeignKeys() {
		return $this->foreignKeys;
	}
}
?>