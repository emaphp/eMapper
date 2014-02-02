<?php
namespace eMapper\Result\Argument;

class PropertyReader {
	/**
	 * Property name
	 * @var string
	 */
	public $property;
	
	public function __construct($property) {
		$this->property = $property;
	}
}
?>