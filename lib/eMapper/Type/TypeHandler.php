<?php
namespace eMapper\Type;

abstract class TypeHandler {
	/**
	 * Casts a given parameter to a valid type accepted by the handler
	 * @param mixed $parameter
	 * @return mixed
	 */
	public function castParameter($parameter) {
		return $parameter;
	}
	
	/**
	 * Returns a valid string expression representing a value
	 * @param mixed $parameter
	 */
	public abstract function setParameter($parameter);
	
	/**
	 * Generates a value from a string obtained from database 
	 * @param string $value
	 */
	public abstract function getValue($value);
}
?>