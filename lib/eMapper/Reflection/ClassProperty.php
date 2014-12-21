<?php
namespace eMapper\Reflection;

use Omocha\AnnotationBag;

/**
 * The ClassProperty class provides access to a property metadata.
 * @author emaphp
 */
class ClassProperty {
	/**
	 * Property name
	 * @var string
	 */
	protected $name;
	
	/**
	 * Reflection property instance
	 * @var \ReflectionProperty
	 */
	protected $reflectionProperty;
	
	/**
	 * Column name
	 * @var string
	 */
	protected $column;
	
	/**
	 * Property type
	 * @var string
	 */
	protected $type;
	
	/**
	 * Indicates if property is primary key
	 * @var bool
	 */
	protected $primaryKey;
	
	/**
	 * Indicates if property is unique
	 * @var bool
	 */
	protected $unique;
	
	/**
	 * Indicates if property must check for duplicates
	 * @var string
	 */
	protected $checkDuplicate;
	
	/**
	 * Indicates if property is read only
	 * @var bool
	 */
	protected $readOnly;
	
	/**
	 * Indicates if property is nullable
	 * @var bool
	 */
	protected $nullable;
	
	public function __construct($propertyName, \ReflectionProperty $reflectionProperty, AnnotationBag $propertyAnnotations) {
		$this->name = $propertyName;
		$this->reflectionProperty = $reflectionProperty;
		$this->reflectionProperty->setAccessible(true);
		
		//parse annotations
		$this->column = $propertyAnnotations->has('Column') ? $propertyAnnotations->get('Column')->getValue() : $propertyName;
		$this->type = $propertyAnnotations->has('Type') ? $propertyAnnotations->get('Type')->getValue() : null;
		$this->primaryKey = $propertyAnnotations->has('Id');
		$this->unique = $propertyAnnotations->has('Unique');
		
		//on duplicate checks
		if ($this->unique && $propertyAnnotations->has('OnDuplicate')) {
			$duplicate = $propertyAnnotations->get('OnDuplicate')->getValue();
			$this->checkDuplicate = !in_array($duplicate, ['ignore', 'update']) ? 'ignore' : $duplicate;
		}
		
		$this->readOnly = $propertyAnnotations->has('ReadOnly');
		$this->nullable = $propertyAnnotations->has('Nullable');
	}
	
	public function getName() {
		return $this->name;
	}
	
	public function getReflectionProperty() {
		return $this->reflectionProperty;
	}
	
	public function getColumn() {
		return $this->column;
	}
	
	public function getAttribute() {
		return $this->attribute;
	}
	
	public function getType() {
		return $this->type;
	}
	
	public function isPrimaryKey() {
		return $this->primaryKey;
	}
	
	public function isUnique() {
		return $this->unique;
	}
	
	public function checksForDuplicates() {
		return !is_null($this->checkDuplicate);
	}
	
	public function getCheckDuplicate() {
		return $this->checkDuplicate;
	}
	
	public function isReadOnly() {
		return $this->readOnly;
	}
	
	public function isNullable() {
		return $this->nullable;
	}
}
