<?php
namespace eMapper\Query;

use eMapper\Reflection\ClassProfile;

/**
 * The Column class represents a table column. Used to build queries for non-declared attributes.
 * @author emaphp
 */
class Column extends Field {
	/**
	 * Returns a new Column instance
	 * @param string $method
	 * @param array $args
	 * @return \eMapper\Query\Column
	 */
	public static function __callstatic($method, $args = null) {
		if (!empty($args))
			return new static($method, $args[0]);
		
		return new static($method);
	}
	
	public function getColumnName(ClassProfile $profile) {
		return $this->name;
	}
}