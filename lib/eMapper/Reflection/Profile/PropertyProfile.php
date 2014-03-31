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
	 * Reflection property instance
	 * @var \ReflectionProperty
	 */
	public $reflectionProperty;
	
	public function __construct($name, AnnotationsBag $prop, \ReflectionProperty $reflectionProperty = null) {
		$this->name = $name;
		$this->reflectionProperty = $reflectionProperty;
		$this->column = $prop->has('map.column') ? $prop->get('map.column') : $name;
		$this->property = $prop->has('map.property') ? $prop->get('map.property') : $name;
		
		if ($prop->has('map.type')) {
			$this->type = $prop->get('map.type');
		}
		elseif ($prop->has('var')) {
			$this->suggestedType = $prop->get('var');
		}
	}
}
?>