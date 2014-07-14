<?php
namespace eMapper\Query;

use eMapper\Reflection\Profile\ClassProfile;

class Attr extends Field {
	public static function __callstatic($method, $args = null) {
		return new Attr($method);
	}
	
	public function getColumnName(ClassProfile $profile) {
		if (!array_key_exists($this->name, $profile->fieldNames)) {
			throw new \RuntimeException(sprintf("Attribute {$this->name} not found in class %s", $profile->reflectionClass->getName()));
		}
		
		return $profile->fieldNames[$this->name];
	}
}
?>