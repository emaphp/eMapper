<?php
namespace eMapper\Engine\SQLite\Type\Handler;

use eMapper\Type\TypeHandler;

class DefaultTypeHandler extends TypeHandler {
	public function getValue($value) {
		return $value;
	}
	
	public function setParameter($parameter) {
		return $parameter;
	}
}
?>