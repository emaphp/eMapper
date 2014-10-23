<?php
namespace eMapper\Type\Handler;

use eMapper\Type\TypeHandler;
use eMapper\Type\ToString;

class StringTypeHandler extends TypeHandler {
	use ToString;
	
	public function getValue($value) {
		return strval($value);
	}
	
	public function castParameter($parameter) {
		if ($parameter instanceof \DateTime)
			return $parameter->format('Y-m-d H:i:s');
		elseif (($parameter = $this->toString($parameter)) === false)
			return null;
		
		return $parameter;
	}
	
	public function setParameter($parameter) {
		return $parameter;
	}
}
?>