<?php
namespace eMapper\Reflection\Profile;

use eMapper\Annotations\AnnotationsBag;

/**
 * The PropertyProfile class provides access to a property metadata.
 * @author emaphp
 */
class PropertyProfile {
	/**
	 * Property name
	 * @var string
	 */
	protected $name;
	
	/**
	 * Referred property (used by parameter maps)
	 * @var string
	 */
	protected $property;
	
	/**
	 * Referred column
	 * @var string
	 */
	protected $column;
	
	/**
	 * Expected type
	 * @var string
	 */
	protected $type;

	/**
	 * Defines if the current property is the primary key
	 * @var boolean
	 */
	protected $primaryKey;
	
	/**
	 * Determines if the property is unique
	 * @var boolean
	 */
	protected $unique;
	
	/**
	 * Determines if the property is not binded to a particular column
	 * @var boolean
	 */
	protected $readOnly;
		
	/**
	 * Reflection property instance
	 * @var \ReflectionProperty
	 */
	protected $reflectionProperty;
	
	public function __construct($name, AnnotationsBag $annotations, \ReflectionProperty $reflectionProperty) {
		$this->name = $name;
		$this->column = $annotations->has('Column') ? $annotations->get('Column')->getValue() : $name;
		$this->property = $annotations->has('Property') ? $annotations->get('Property')->getValue() : $name;
		
		if ($annotations->has('Type')) {
			$this->type = $annotations->get('Type')->getValue();
		}
		
		$this->primaryKey = $annotations->has('Id');
		$this->unique = $annotations->has('Unique');
		$this->readOnly = $annotations->has('ReadOnly');
		
		$this->reflectionProperty = $reflectionProperty;
		$this->reflectionProperty->setAccessible(true);
	}
	
	/**
	 * Obtains property name
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}
	
	/**
	 * Obtains referred property (parameter maps)
	 * @return string
	 */
	public function getProperty() {
		return $this->property;
	}
	
	/**
	 * Obtains referred column
	 * @return string
	 */
	public function getColumn() {
		return $this->column;
	}
	
	/**
	 * Obtains property type
	 * @return string
	 */
	public function getType() {
		return $this->type;
	}
	
	/**
	 * Determines if the property is primary key (entities only)
	 * @return boolean
	 */
	public function isPrimaryKey() {
		return $this->primaryKey;
	}
	
	/**
	 * Determines if the property is unique (entities only)
	 * @return boolean
	 */
	public function isUnique() {
		return $this->unique;
	}
	
	/**
	 * Determines if the property is read only
	 * @return boolean
	 */
	public function isReadOnly() {
		return $this->readOnly;
	}
	
	/**
	 * Obtains the reflection property
	 * @return ReflectionProperty
	 */
	public function getReflectionProperty() {
		return $this->reflectionProperty;
	}
}
?>