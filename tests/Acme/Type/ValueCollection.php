<?php
namespace Acme\Type;

class ValueCollection extends \ArrayObject {
	public function __toString() {
		return implode(',', $this->getArrayCopy());
	}
}
?>