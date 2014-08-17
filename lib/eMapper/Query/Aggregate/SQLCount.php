<?php
namespace eMapper\Query\Aggregate;

use eMapper\Reflection\Profile\ClassProfile;

/**
 * The SQLCount class represents the generic COUNT aggregate function.
 * @author emaphp
 */
class SQLCount extends SQLFunction {
	public function __construct() {
	}
	
	public function getExpression(ClassProfile $profile, $alias = '') {
		return empty($alias) ? 'COUNT(*)' : "COUNT($alias.*)";
	}
}
?>