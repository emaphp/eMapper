<?php
namespace eMapper\Type\Handler;

use eMapper\Type\TypeHandler;

/**
 * @unquoted
 */
class FloatTypeHandler extends TypeHandler {
	public function getValue($value) {
		return (float) $value;
	}
	
	public function castParameter($parameter) {
		if (is_float($parameter)) {
			return $parameter;
		}
		elseif (is_integer($parameter) || is_bool($parameter) || is_string($parameter)) {
			return (float) $parameter;
		}
		
		return null;
	}
	
	public function setParameter($parameter) {
		return $parameter;
	}
}
?>