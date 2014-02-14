<?php
namespace eMapper\Type\Handler;

use eMapper\Type\TypeHandler;

/**
 * @unquoted
 */
class BooleanTypeHandler extends TypeHandler {
	protected function isTrue($value) {
		if (is_string($value) && (strtolower($value) == 'f' || strtolower($value) == 'false')) {
			return false;
		}
	
		return (bool) $value;
	}
	
	public function getValue($value) {
		return $this->isTrue($value);
	}
	
	public function castParameter($parameter) {
		return $this->isTrue($parameter);
	}
	
	public function setParameter($parameter) {
		return ($parameter) ? 'TRUE' : 'FALSE';
	}
}
?>