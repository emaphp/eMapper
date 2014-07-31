<?php
namespace eMapper\Type;

/**
 * The ValueExport trait provides basic type conversion methods to/from strings
 * Used: eMapper\Cache\CacheKey
 * 
 * @author emaphp
 */
trait ValueExport {
	/**
	 * Casts a given value to string
	 * If that value can´t be casted to string then false is returned
	 * Returns NULL for NULL values
	 * @param mixed $value
	 * @return NULL|boolean|string
	 */
	public function toString($value) {
		if (is_null($value)) {
			return null;
		}
		elseif (is_string($value)) {
			return $value;
		}
		elseif (is_resource($value) || is_array($value)) {
			return false;
		}
		elseif (is_object($value)) {
			if (method_exists($value, '__toString')) {
				$rc = new \ReflectionClass($value);
				$rm = $rc->getMethod('__toString');
	
				if ($rm->isPublic()) {
					return $value->__toString();
				}
	
				return false;
			}
	
			return false;
		}
	
		return strval($value);
	}
	
	/**
	 * Obtains a string representation for a value
	 * If that value can´t be casted to string then false is returned
	 * @param mixed $value
	 * @return string|boolean
	 */
	public function asString($value) {
		switch (gettype($value)) {
			case 'NULL':
				return 'NULL';
				break;
					
			case 'integer':
			case 'double':
				return strval($value);
				break;
					
			case 'string':
				return $value;
				break;
	
			case 'boolean':
				return ($value) ? 'TRUE' : 'FALSE';
				break;
	
			case 'object': {
				if (method_exists($value, '__toString')) {
					$rc = new \ReflectionClass($value);
					$rm = $rc->getMethod('__toString');
	
					if ($rm->isPublic()) {
						return $value->__toString();
					}
	
					return false;
				}
				
				return false;
			}
			break;
				
			default:
				return false;
		}
	}
}
?>