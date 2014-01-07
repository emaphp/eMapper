<?php
namespace eMapper\Type\Handler;

use eMapper\Type\TypeHandler;
use eMapper\Type\ValueExport;

class StringTypeHandler extends TypeHandler {
	use ValueExport;
	
	public function getValue($value) {
		return $value;
	}
	
	public function castParameter($parameter) {
		if ($parameter instanceof \DateTime) {
			return $parameter->format('Y-m-d H:i:s');
		}
		elseif (($parameter = $this->toString($parameter)) === false) {
			return null;
		}
		
		return $parameter;
	}
	
	public function setParameter($parameter) {
		return $parameter;
	}
}
?>