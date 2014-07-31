<?php
namespace eMapper\Query;

use eMapper\Reflection\Profile\ClassProfile;

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
		if (!empty($args)) {
			return new Column($method, $args[0]);
		}
		
		return new Column($method);
	}
	
	public function getColumnName(ClassProfile $profile) {
		return $this->name;
	}
}
?>