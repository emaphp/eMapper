<?php
namespace eMapper\Type\Handler;

use eMapper\Type\TypeHandler;

class DateTypeHandler extends TypeHandler {
	public function getValue($value) {
		return new \DateTime($value);
	}
	
	public function castParameter($parameter) {
		if ($parameter instanceof \DateTime) {
			return $parameter;
		}
		elseif (is_string($parameter)) {
			return new \DateTime($parameter);
		}
		elseif (is_integer($parameter)) {
			return new \DateTime('@' . $parameter);
		}
		
		return null;
	}
	
	public function setParameter($parameter) {
		return $parameter->format('Y-m-d');
	}
}
?>