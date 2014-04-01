<?php
namespace eMapper\Engine\SQLite\Type\Handler;

use eMapper\Type\Handler\BooleanTypeHandler as TypeHandler;

class BooleanTypeHandler extends TypeHandler {
	public function setParameter($parameter) {
		return ($parameter) ? 1 : 0;
	}
}
?>