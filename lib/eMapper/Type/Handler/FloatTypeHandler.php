<?php
namespace eMapper\Type\Handler;

use eMapper\Type\TypeHandler;

/**
 * @Safe
 */
class FloatTypeHandler extends TypeHandler {
	public function getValue($value) {
		return floatval($value);
	}
	
	public function castParameter($parameter) {
		if (is_float($parameter))
			return $parameter;
		elseif (is_integer($parameter) || is_bool($parameter) || is_string($parameter))
			return floatval($parameter);
		
		return null;
	}
	
	public function setParameter($parameter) {
		return $parameter;
	}
}