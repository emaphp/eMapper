<?php
namespace eMapper\Query;

use eMapper\Reflection\Profile\ClassProfile;

/**
 * The Attr class represents an entity attribute.
 * @author emaphp
 */
class Attr extends Field {
	/**
	 * Returns a new Attr instance
	 * @param string $method
	 * @param array $args
	 * @return \eMapper\Query\Attr
	 */
	public static function __callstatic($method, $args = null) {
		if (!empty($args)) {
			return new Attr($method, $args[0]);
		}
		
		return new Attr($method);
	}
	
	public function getColumnName(ClassProfile $profile) {
		$propertyNames = $profile->getPropertyNames();
		
		if (!array_key_exists($this->name, $propertyNames)) {
			throw new \RuntimeException(sprintf("Attribute {$this->name} not found in class %s", $profile->getReflectionClass()->getName()));
		}
		
		return $propertyNames[$this->name];
	}
}
?>