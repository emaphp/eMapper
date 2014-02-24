<?php
namespace eMapper\Type\Handler;

use eMapper\Type\TypeHandler;

class JSONTypeHandler extends TypeHandler {
	public function getValue($value) {
		return json_decode($value);
	}
	
	public function setParameter($parameter) {
		return json_encode($parameter);
	}
}
?>