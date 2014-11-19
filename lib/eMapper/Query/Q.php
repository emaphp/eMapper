<?php
namespace eMapper\Query;

use eMapper\SQL\Predicate\Filter;

/**
 * The Q class build instances of Filter dynamically
 * @author emaphp
 */
abstract class Q {
	/*
	 * OPERATORS
	 */
	const LOGICAL_OR = 'OR';
	const LOGICAL_AND = 'AND';
	
	/**
	 * Builds an OR filter
	 * Ex: Q::where(Attr::category()->eq('E-books'), Attr::category()->eq('Smartphones'))
	 * @return \eMapper\Query\Predicate\Filter
	 */
	public static function where() {
		return new Filter(func_get_args(), false, self::LOGICAL_OR);
	}
	
	/**
	 * Builds a negated OR filter
	 * Ex: Q::where_not(Attr::category()->eq('E-books'), Attr::category()->eq('Smartphones'))
	 * @return \eMapper\Query\Predicate\Filter
	 */
	public static function where_not() {
		return new Filter(func_get_args(), true, self::LOGICAL_OR);
	}
	
	/**
	 * Builds a AND filter
	 * @return \eMapper\Query\Predicate\Filter
	 */
	public static function filter() {
		return new Filter(func_get_args(), false, self::LOGICAL_AND);
	}
	
	/**
	 * Builds a negated AND filter
	 * @return \eMapper\Query\Predicate\Filter
	 */
	public static function exclude() {
		return new Filter(func_get_args(), true, self::LOGICAL_AND);
	}
}
?>