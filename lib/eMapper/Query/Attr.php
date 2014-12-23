<?php
namespace eMapper\Query;

use eMapper\Reflection\ClassProfile;

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
		if (!empty($args))
			return new static($method, $args[0]);
		return new static($method);
	}
	
	public function getColumnName(ClassProfile $profile) {		
		if (!$profile->hasProperty($this->name))
			throw new \RuntimeException(sprintf("Attribute {$this->name} not found in class %s", $profile->getReflectionClass()->getName()));
		
		return $profile->getProperty($this->name)->getColumn();
	}
}