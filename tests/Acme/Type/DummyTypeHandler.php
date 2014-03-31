<?php
namespace Acme\Type;

use eMapper\Type\TypeHandler;

/**
 * @meta.parser emapper\emapper
 * @map.unquoted
 */
class DummyTypeHandler extends TypeHandler {
	public function getValue($value) {
	}
	
	public function setParameter($parameter) {
	}
}
?>