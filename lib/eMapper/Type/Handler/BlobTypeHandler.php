<?php
namespace eMapper\Type\Handler;

use eMapper\Type\TypeHandler;
use eMapper\Type\ToString;

/**
 * @Safe
 */
class BlobTypeHandler extends TypeHandler {
	use ToString;
	
	public function getValue($value) {
		return $value;
	}
	
	public function castParameter($parameter) {
		if (($parameter = $this->toString($parameter)) === false)
			return null;
		
		return $parameter;
	}
	
	public function setParameter($parameter) {
		return "x'" . bin2hex($parameter) . "'";
	}
}