<?php
namespace eMapper\Reflection\Argument;

/**
 * The ArrayArgumentWrapper class provides an interface for accessing array keys.
 * @author emaphp
 */
class ArrayArgumentWrapper extends ArgumentWrapper {
	public function offsetExists($offset) {
		return array_key_exists($offset, $this->value);
	}
	
	public function offsetGet($offset) {
		return $this->value[$offset];
	}
	
	public function offsetSet($offset, $value) {
		$this->value[$offset] = $value;
	}
	
	public function offsetUnset($offset) {
		unset($this->value[$offset]);
	}
}
