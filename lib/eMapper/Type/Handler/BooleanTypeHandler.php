<?php
namespace eMapper\Type\Handler;

use eMapper\Type\TypeHandler;

/**
 * @unquoted
 */
class BooleanTypeHandler extends TypeHandler {
	public function getValue($value) {
		if (is_string($value) && (strtolower($value) == 'f' || strtolower($value) == 'false')) {
			return false;
		}
		
		return (bool) $value;
	}
	
	public function castParameter($parameter) {
		if (is_string($parameter) && (strtolower($parameter) == 'f' || strtolower($parameter) == 'false')) {
			return false;
		}
		
		return (bool) $parameter;
	}
	
	public function setParameter($parameter) {
		return ($parameter) ? 'TRUE' : 'FALSE';
	}
}
?>