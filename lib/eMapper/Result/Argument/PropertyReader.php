<?php
namespace eMapper\Result\Argument;

class PropertyReader {
	public $property;
	
	public function __construct($property) {
		$this->property = $property;
	}
}
?>