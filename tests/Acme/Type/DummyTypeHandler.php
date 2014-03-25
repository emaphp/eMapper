<?php
namespace Acme\Type;

use eMapper\Type\TypeHandler;

/**
 * @parser emapper\emapper
 * @unquoted
 */
class DummyTypeHandler extends TypeHandler {
	public function getValue($value) {
	}
	
	public function setParameter($parameter) {
	}
}
?>