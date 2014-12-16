<?php
namespace eMapper\Query;

use eMapper\SQL\Predicate\Filter;

/**
 * The Cond class build instances of Filter dynamically
 * @author emaphp
 */
abstract class Cond {
	/*
	 * OPERATORS
	 */
	const LOGICAL_OR = 'OR';
	const LOGICAL_AND = 'AND';
	
	/**
	 * Builds an OR filter
	 * Ex: Cond::orfilter(Attr::category()->eq('E-books'), Attr::category()->eq('Smartphones'))
	 * @return \eMapper\SQL\Predicate\Filter
	 */
	public static function orfilter() {
		return new Filter(func_get_args(), false, self::LOGICAL_OR);
	}
	
	/**
	 * Builds a negated OR filter
	 * Ex: Cond::orexclude(Attr::category()->eq('E-books'), Attr::category()->eq('Smartphones'))
	 * @return \eMapper\SQL\Predicate\Filter
	 */
	public static function orexclude() {
		return new Filter(func_get_args(), true, self::LOGICAL_OR);
	}
	
	/**
	 * Builds a AND filter
	 * @return \eMapper\SQL\Predicate\Filter
	 */
	public static function filter() {
		return new Filter(func_get_args(), false, self::LOGICAL_AND);
	}
	
	/**
	 * Builds a negated AND filter
	 * @return \eMapper\SQL\Predicate\Filter
	 */
	public static function exclude() {
		return new Filter(func_get_args(), true, self::LOGICAL_AND);
	}
}