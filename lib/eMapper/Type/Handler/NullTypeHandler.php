<?php
namespace eMapper\Type\Handler;

use eMapper\Type\TypeHandler;

/**
 * @map.unquoted
 */
class NullTypeHandler extends TypeHandler {
	public function getValue($value) {
		return null;
	}
	
	public function setParameter($parameter) {
		return null;
	}
}
?>