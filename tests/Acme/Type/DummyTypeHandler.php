<?php
namespace Acme\Type;

use eMapper\Type\TypeHandler;

/**
 * @TypeHandler
 * @Safe
 */
class DummyTypeHandler extends TypeHandler {
	public function getValue($value) {
	}
	
	public function setParameter($parameter) {
	}
}
?>