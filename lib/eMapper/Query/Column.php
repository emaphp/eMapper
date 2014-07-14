<?php
namespace eMapper\Query;

use eMapper\Reflection\Profile\ClassProfile;

class Column extends Field {
	public function __callstatic($method, $args = null) {
		return new Column($method);
	}
	
	public function getColumnName(ClassProfile $profile) {
		return $this->name;
	}
}

?>