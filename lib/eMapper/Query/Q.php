<?php
namespace eMapper\Query;

use eMapper\Query\Predicate\Filter;

abstract class Q {
	const LOGICAL_OR = 'OR';
	const LOGICAL_AND = 'AND';
	
	public static function when() {
		return new Filter(func_get_args(), false, self::LOGICAL_OR);
	}
	
	public static function when_not() {
		return new Filter(func_get_args(), true, self::LOGICAL_OR);
	}
	
	public static function filter() {
		return new Filter(func_get_args(), false, self::LOGICAL_AND);
	}
	
	public static function exclude() {
		return new Filter(func_get_args(), true, self::LOGICAL_AND);
	}
}
?>