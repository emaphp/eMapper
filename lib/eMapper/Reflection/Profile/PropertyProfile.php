<?php
namespace eMapper\Reflection\Profile;

use Omocha\AnnotationBag;

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
	 * Attribute name
	 * @var string
	 */
	protected $attribute;
	
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
	 * Duplicate field checks
	 * @var boolean
	 */
	protected $checkDuplicate;
	
	/**
	 * Determines if the property is not binded to a particular column
	 * @var boolean
	 */
	protected $readOnly;
	
	/**
	 * Determines if the property is nullable
	 * @var boolean
	 */
	protected $nullable;
	
	/**
	 * Reflection property instance
	 * @var \ReflectionProperty
	 */
	protected $reflectionProperty;
	
	public function __construct($name, AnnotationBag $annotations, \ReflectionProperty $reflectionProperty) {
		$this->name = $name;
		$this->column = $annotations->has('Column') ? $annotations->get('Column')->getValue() : $name;
		$this->attribute = $annotations->has('Attr') ? $annotations->get('Attr')->getValue() : $name;
		
		if ($annotations->has('Type')) {
			$this->type = $annotations->get('Type')->getValue();
		}
		
		$this->primaryKey = $annotations->has('Id');
		$this->unique = $annotations->has('Unique');
		
		if ($this->unique && $annotations->has('CheckDuplicate')) {
			$duplicate = $annotations->get('CheckDuplicate')->getValue();
			$this->checkDuplicate = !in_array($duplicate, ['ignore', 'update']) ? 'ignore' : $duplicate;
		}
		
		$this->readOnly = $annotations->has('ReadOnly');
		$this->nullable = $annotations->has('Nullable');
		
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
	 * Obtains attribute
	 * @return string
	 */
	public function getAttribute() {
		return $this->attribute;
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
	 * Determines if the attribute is unique and must check for a duplicated value before insertion
	 * @return boolean
	 */
	public function mustCheckDuplicate() {
		return !is_null($this->checkDuplicate);
	}
	
	/**
	 * Returns the duplicate action associated to the current property
	 * @return string
	 */
	public function getCheckDuplicate() {
		return $this->checkDuplicate;
	}
	
	/**
	 * Determines if the property is read only
	 * @return boolean
	 */
	public function isReadOnly() {
		return $this->readOnly;
	}
	
	/**
	 * Determines if the current property can take null values
	 * @return boolean
	 */
	public function isNullable() { 
		return $this->nullable;
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