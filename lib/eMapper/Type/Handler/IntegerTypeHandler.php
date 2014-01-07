<?php
namespace eMapper\Type\Handler;

use eMapper\Type\TypeHandler;

/**
 * @unquoted
 */
class IntegerTypeHandler extends TypeHandler {
	public function getValue($value) {
		return (int) $value;
	}
	
	public function castParameter($parameter) {
		if (is_integer($parameter)) {
			return $parameter;
		}
		elseif (is_string($parameter) || is_float($parameter) || is_bool($parameter)) {
			return (int) $parameter;
		}
		
		return null;
	}
	
	public function setParameter($parameter) {
		return $parameter;
	}
}
?>