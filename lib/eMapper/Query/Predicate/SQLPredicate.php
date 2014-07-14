<?php
namespace eMapper\Query\Predicate;

use eMapper\Reflection\Profile\ClassProfile;
use eMapper\Engine\Generic\Driver;

abstract class SQLPredicate {
	protected static $counter = 0;
	protected $negate = false;
	
	public static function argNumber() {
		return self::$counter++;
	}
	
	public abstract function evaluate(Driver $driver, ClassProfile $profile, &$args, $arg_index = 0);
}
?>