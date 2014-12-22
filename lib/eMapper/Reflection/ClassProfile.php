<?php
namespace eMapper\Reflection;

use Omocha\Omocha;
use Omocha\AnnotationBag;
use eMapper\ORM\Dynamic\Query;
use eMapper\ORM\Dynamic\Statement;
use eMapper\ORM\Dynamic\Procedure;
use eMapper\ORM\Dynamic\Macro;
use eMapper\ORM\Association\OneToOne;
use eMapper\ORM\Association\OneToMany;
use eMapper\ORM\Association\ManyToOne;
use eMapper\ORM\Association\ManyToMany;

/**
 * The ClassProfile class provides details of the implementation and metadata available in a class.
 * @author emaphp
 */
class ClassProfile {
	/**
	 * Reflection class instance
	 * @var \ReflectionClass
	 */
	protected $reflectionClass;
	
	/**
	 * Class annotations
	 * @var \Omocha\AnnotationBag
	 */
	protected $classAnnotations;
	
	/**
	 * Class properties
	 * @var array:\eMapper\Reflection\ClassProperty
	 */
	protected $properties = [];
	
	/**
	 * Primary key property
	 * @var string
	 */
	protected $primaryKey;
	
	/**
	 * Read-only properties
	 * @var array:string
	 */
	protected $readOnlyProperties;
	
	/**
	 * Property to column map
	 * @var array[string]:string
	 */
	protected $propertyMap;
	
	/**
	 * Class attributes
	 * @var array:\eMapper\ORM\Dynamic\DynamicAttribute
	 */
	protected $attributes;
	
	/**
	 * Class dynamic attributes
	 * @var array:\eMapper\ORM\Dynamic\DynamicAttribute
	 */
	protected $dynamicAttributes;
	
	/**
	 * Class associations
	 * @var array:\eMapper\ORM\Association\Association
	 */
	protected $associations;
	
	/**
	 * Properties that reference another entity
	 * @var array:string
	 */
	protected $references;
	
	/**
	 * Properties that are used to solve an association
	 * @var array[string]:string
	 */
	protected $foreignKeys;
	
	/**
	 * Properties that require a duplication check
	 * @var array:string
	 */
	protected $duplicateChecks;
	
	public function __construct($className) {
		try {
			$this->reflectionClass = new \ReflectionClass($className);
		}
		catch(\ReflectionException $re) {
			throw new \InvalidArgumentException($re->getMessage());
		}
		
		//obtain class annotations
		$this->classAnnotations = Omocha::getAnnotations($this->reflectionClass);
		$this->parseProperties();
	}
	
	/**
	 * Determines if a given property is a dyamic attribute
	 * @param \Omocha\AnnotationBag $propertyAnnotations
	 * @return boolean
	 */
	protected function isDynamicAttribute(AnnotationBag $propertyAnnotations) {
		return $propertyAnnotations->has('Query') ||
			$propertyAnnotations->has('Statement') ||
			$propertyAnnotations->has('Procedure') ||
			$propertyAnnotations->has('Eval');
	}
	
	/**
	 * Parses a dynamic attribute
	 * @param string$propertyName
	 * @param \ReflectionProperty $reflectionProperty
	 * @param \Omocha\AnnotationBag $propertyAnnotations
	 */
	protected function parseDynamicAttribute($propertyName, \ReflectionProperty $reflectionProperty, AnnotationBag $propertyAnnotations) {
		if ($propertyAnnotations->has('Query'))
			$attribute = new Query($propertyName, $reflectionProperty, $propertyAnnotations);
		elseif ($propertyAnnotations->has('Statement'))
			$attribute = new Statement($propertyName, $reflectionProperty, $propertyAnnotations);
		elseif ($propertyAnnotations->has('Procedure'))
			$attribute = new Procedure($propertyName, $reflectionProperty, $propertyAnnotations);
		elseif ($propertyAnnotations->has('Eval'))
			$attribute = new Macro($propertyName, $reflectionProperty, $propertyAnnotations);
		
		if ($attribute->isCacheable())
			$this->attributes[$propertyName] = $attribute;
		else
			$this->dynamicAttributes[$propertyName] = $attribute;
	}
	
	/**
	 * Determines if a given property is an association
	 * @param \Omocha\AnnotationBag $propertyAnnotations
	 * @return boolean
	 */
	protected function isAssociation(AnnotationBag $propertyAnnotations) {
		return $propertyAnnotations->has('OneToOne') ||
			$propertyAnnotations->has('OneToMany') ||
			$propertyAnnotations->has('ManyToOne') ||
			$propertyAnnotations->has('ManyToMany');
	}
	
	/**
	 * Parses an entity association
	 * @param string $propertyName
	 * @param \ReflectionProperty $reflectionProperty
	 * @param \Omocha\AnnotationBag $propertyAnnotations
	 */
	protected function parseAssociation($propertyName, \ReflectionProperty $reflectionProperty, AnnotationBag $propertyAnnotations) {
		if ($propertyAnnotations->has('OneToOne')) {
			$association = new OneToOne($propertyName, $reflectionProperty, $propertyAnnotations);
			
			if ($association->isForeignKey()) {
				$attribute = $association->getAttribute()->getArgument();
				$this->foreignKeys[$attribute] = $propertyName;
			}
			elseif ($association->isCascade())
				$this->references[] = $propertyName;
		}
		elseif ($propertyAnnotations->has('OneToMany')) {
			$association = new OneToMany($propertyName, $reflectionProperty, $propertyAnnotations);
			
			if ($association->isCascade())
				$this->references[] = $propertyName;
		}
		elseif ($propertyAnnotations->has('ManyToOne')) {
			$association = new ManyToOne($propertyName, $reflectionProperty, $propertyAnnotations);
			$attribute = $association->getAttribute()->getArgument();
			$this->foreignKeys[$attribute] = $propertyName;
		}
		elseif ($propertyAnnotations->has('ManyToMany')) {
			$association = new ManyToMany($propertyName, $reflectionProperty, $propertyAnnotations);
			
			if ($association->isCascade())
				$this->references[] = $propertyName;
		}
		
		$this->associations[$propertyName] = $association;
	}
	
	/**
	 * Parses the properties of the current class
	 */
	protected function parseProperties() {
		if ($this->isEntity()) {
			//property mapping
			$this->propertyMap = [];
			//dynamic attributes
			$this->attributes = $this->dynamicAttributes = [];
			//associations
			$this->associations = $this->foreignkeys = $this->references = [];
			//read-only properties
			$this->readOnlyProperties = [];
			//duplicate checks
			$this->duplicateChecks = [];
		}
		
		$properties = $this->reflectionClass->getProperties();
		
		foreach ($properties as $reflectionProperty) {
			$propertyName = $reflectionProperty->getName();
			$propertyAnnotations = Omocha::getAnnotations($reflectionProperty);
			
			if ($this->isEntity() && $this->isDynamicAttribute($propertyAnnotations))
				$this->parseDynamicAttribute($propertyName, $reflectionProperty, $propertyAnnotations);
			elseif ($this->isEntity() && $this->isAssociation($propertyAnnotations))
				$this->parseAssociation($propertyName, $reflectionProperty, $propertyAnnotations);
			else {
				$property = new ClassProperty($propertyName, $reflectionProperty, $propertyAnnotations);
				
				if ($this->isEntity()) {
					if ($property->isPrimaryKey())
						$this->primaryKey = $propertyName;
					elseif ($property->checksForDuplicates())
						$this->duplicateChecks[] = $propertyName;
						
					$this->propertyMap[$propertyName] = $property->getColumn();
					
					if ($property->isReadOnly())
						$this->readOnlyProperties[] = $propertyName;
				}
				
				$this->properties[$propertyName] = $property;
			}
		}
	}
	
	/**
	 * Obtains reflection class instance
	 * @return \ReflectionClass
	 */
	public function getReflectionClass() {
		return $this->reflectionClass;
	}
	
	/**
	 * Obtains class annotations
	 * @return \Omocha\AnnotationBag
	 */
	public function getAnnotations() {
		return $this->classAnnotations;
	}
	
	/**
	 * Obtains class properties
	 * @return array:\eMapper\Reflection\ClassProperty
	 */
	public function getProperties() {
		return $this->properties;
	}
	
	/**
	 * Determines if the given property exists on the current class
	 * @param string $property
	 * @return boolean
	 */
	public function hasProperty($property) {
		return array_key_exists($property, $this->properties);
	}
	
	/**
	 * Obtains a class property
	 * @param string $property
	 * @return \eMapper\Reflection\ClassProperty
	 */
	public function getProperty($property) {
		return $this->properties[$property];
	}
	
	/*
	 * ENTITIES
	 */
	
	/**
	 * Finds whether current class is a valid entity
	 * @return boolean
	 */
	public function isEntity() {
		return $this->classAnnotations->has('Entity');
	}
	
	/**
	 * Obtains class read-only properties
	 * @return array:string
	 */
	public function getReadOnlyProperties() {
		return $this->readOnlyProperties;
	}
	
	/**
	 * Obtains entity referred table
	 * @return mixed
	 */
	public function getEntityTable() {
		return $this->classAnnotations->get('Entity')->getValue();
	}
	
	/**
	 * Obtains entity property map
	 * @return array[string]:string
	 */
	public function getPropertyMap() {
		return $this->propertyMap;
	}
	
	/**
	 * Obtains primary key property
	 * @return \eMapper\Reflection\ClassProperty
	 */
	public function getPrimaryKeyProperty() {
		return $this->properties[$this->primaryKey];
	}
	
	/**
	 * Obtains primary key property name
	 * When $asColumn is true it returns the associated column name
	 * @param string $asColumn
	 */
	public function getPrimaryKey($asColumn = false) {
		if ($asColumn)
			return $this->properties[$this->primaryKey]->getColumn();

		return $this->primaryKey;
	}
	
	/**
	 * Obtains class cacheable attributes
	 * @return array:\eMapper\ORM\Dynamic\DynamicAttribute
	 */
	public function getAttributes() {
		return $this->attributes;
	}
	
	/**
	 * Obtains class dynamic attributes
	 * @return array:\eMapper\ORM\Dynamic\DynamicAttribute
	 */
	public function getDynamicAttributes() {
		return $this->dynamicAttributes;
	}
	
	/**
	 * Obtains class associations
	 * @return array:\eMapper\ORM\Association\Association
	 */
	public function getAssociations() {
		return $this->associations;
	}
	
	/**
	 * Determines if the given association exists
	 * @param string $association
	 * @return boolean
	 */
	public function hasAssociation($association) {
		return array_key_exists($association, $this->associations);
	}
	
	/**
	 * Obtains an association by name
	 * @param string $association
	 * @return \eMapper\ORM\Association\Association
	 */
	public function getAssociation($association) {
		return $this->associations[$association];
	}
	
	/**
	 * Finds whether entity has foreign keys
	 * @return boolean
	 */
	public function hasForeignKeys() {
		return !empty($this->foreignKeys);
	}
	
	/**
	 * Obtains class foreign keys
	 * @return array[string]:string
	 */
	public function getForeignKeys() {
		return $this->foreignKeys;
	}
	
	/**
	 * Obtains entity references
	 * @return array:string
	 */
	public function getReferences() {
		return $this->references;
	}
	
	/**
	 * Obtains class duplicate checks
	 * @return array:string
	 */
	public function getDuplicateChecks() {
		return $this->duplicateChecks;
	}
	
	/**
	 * Obtains a list of attributes for SELECT statements
	 * @return array:string
	 */	
	public function getSelectAttributes() {
		return array_keys(array_unique($this->propertyMap));
	}
	
	/**
	 * Obtains a list of columns for SELECT statements
	 * @return array:string
	 */
	public function getSelectColumns() {
		return array_values(array_unique($this->propertyMap));
	}
	
	/**
	 * Obtains a list of attributes for INSERT statements
	 * @return array:string
	 */
	public function getInsertAttributes() {
		return array_diff(array_keys($this->propertyMap), $this->readOnlyProperties, [$this->primaryKey]);
	}
	
	/*
	 * TYPE HANDLERS
	 */
	
	/**
	 * Determines if the current class is a type handler
	 * @return boolean
	 */
	public function isTypeHandler() {
		return $this->reflectionClass->isSubclassOf('eMapper\Type\TypeHandler');
	}
	
	/**
	 * DEtermines if a type handler provides safe values
	 * @return boolean
	 */
	public function isSafe() {
		if ($this->classAnnotations->has('Safe'))
			return (bool) $this->classAnnotations->get('Safe')->getValue();
		
		return false;
	}
}