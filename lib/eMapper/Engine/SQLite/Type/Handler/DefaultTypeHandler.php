<?php
namespace eMapper\Engine\SQLite\Type\Handler;

use eMapper\Type\TypeHandler;

/**
 * The DefaultTypeHandler class handles all values recovered from the database.
 * @author emaphp
 */
class DefaultTypeHandler extends TypeHandler {
	public function getValue($value) {
		return $value;
	}
	
	public function setParameter($parameter) {
		return $parameter;
	}
}
?>