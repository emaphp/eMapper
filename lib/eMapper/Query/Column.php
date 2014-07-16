<?php
namespace eMapper\Query;

use eMapper\Reflection\Profile\ClassProfile;

class Column extends Field {
	public static function __callstatic($method, $args = null) {
		return new Column($method);
	}
	
	public function getColumnName(ClassProfile $profile) {
		if (!in_array($this->name, $profile->fieldNames)) {
			throw new \RuntimeException(sprintf("Column %s not found in class %s", $this->name, $profile->reflectionClass->getName()));
		}
		
		return $this->name;
	}
}

?>