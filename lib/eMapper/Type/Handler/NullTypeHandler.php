<?php
namespace eMapper\Type\Handler;

use eMapper\Type\TypeHandler;

/**
 * @unquoted
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