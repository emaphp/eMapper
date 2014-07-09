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
	 * Referenced property
	 * @var string
	 */
	public $property;
	
	/**
	 * Referenced column
	 * @var string
	 */
	public $column;
	
	/**
	 * Expected type
	 * @var string
	 */
	public $type;
	
	/**
	 * Suggested type
	 * @var string
	 */
	public $suggestedType;
	
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
	
	public function __construct($name, AnnotationsBag $prop, \ReflectionProperty $reflectionProperty) {
		$this->name = $name;
		$this->column = $prop->has('map.column') ? $prop->get('map.column') : $name;
		$this->property = $prop->has('map.property') ? $prop->get('map.property') : $name;
		
		if ($prop->has('map.type')) {
			$this->type = $prop->get('map.type');
		}
		elseif ($prop->has('var')) {
			$this->suggestedType = $prop->get('var');
		}
		
		$this->isPrimaryKey = $prop->has('map.pk');
		
		$this->reflectionProperty = $reflectionProperty;
		$this->reflectionProperty->setAccessible(true);
	}
}
?>