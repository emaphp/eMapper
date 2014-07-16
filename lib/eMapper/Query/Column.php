<?php
namespace eMapper\Query;

use eMapper\Reflection\Profile\ClassProfile;

class Column extends Field {
	public static function __callstatic($method, $args = null) {
		if (!empty($args)) {
			return new Column($method, $args[0]);
		}
		
		return new Column($method);
	}
	
	public function getColumnName(ClassProfile $profile) {
		//avoid checking if the column is declarated within the class
		//this allows querying by columns not added as properties
// 		if (!in_array($this->name, $profile->fieldNames)) {
// 			throw new \RuntimeException(sprintf("Column %s not found in class %s", $this->name, $profile->reflectionClass->getName()));
// 		}
		
		return $this->name;
	}
}

?>