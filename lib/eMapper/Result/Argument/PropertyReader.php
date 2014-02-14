<?php
namespace eMapper\Result\Argument;

class PropertyReader {
	/**
	 * Property name
	 * @var string
	 */
	public $property;
	
	/**
	 * Property type
	 * @var string | NULL
	 */
	public $type;
	
	public function __construct($property, $type = null) {
		$this->property = $property;
		$this->type = $type;
	}
}
?>