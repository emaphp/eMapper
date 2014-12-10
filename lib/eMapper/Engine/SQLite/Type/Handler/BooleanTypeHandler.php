<?php
namespace eMapper\Engine\SQLite\Type\Handler;

use eMapper\Type\Handler\BooleanTypeHandler as TypeHandler;

/**
 * The BooleanTypeHandler class handles the conversion from boolean values to a supported type.
 * @author emaphp
 */
class BooleanTypeHandler extends TypeHandler {
	public function setParameter($parameter) {
		return ($parameter) ? 1 : 0;
	}
}