<?php
namespace eMapper\Reflection\Profile;

use Minime\Annotations\AnnotationsBag;

class PropertyProfile {
	/**
	 * Property name
	 * @var string
	 */
	public $name;
	
	/**
	 * Referred property
	 * @var string
	 */
	public $property;
	
	/**
	 * Referred column
	 * @var string
	 */
	public $column;
	
	/**
	 * Expected type
	 * @var string
	 */
	public $type;

	/**
	 * Defines if the current property is the primary key
	 * @var boolean
	 */
	public $isPrimaryKey;
	
	/**
	 * Reflection property instance
	 * @var \ReflectionProperty
	 */
	public $reflectionProperty;
	
	public function __construct($name, AnnotationsBag $annotations, \ReflectionProperty $reflectionProperty) {
		$this->name = $name;
		$this->column = $annotations->has('Column') ? $annotations->get('Column') : $name;
		$this->property = $annotations->has('Property') ? $annotations->get('Property') : $name;
		
		if ($annotations->has('Type')) {
			$this->type = $annotations->get('Type');
		}
		
		$this->isPrimaryKey = $annotations->has('Id');
		
		$this->reflectionProperty = $reflectionProperty;
		$this->reflectionProperty->setAccessible(true);
	}
}
?>