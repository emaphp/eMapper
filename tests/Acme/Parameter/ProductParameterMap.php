<?php
namespace Acme\Parameter;

/**
 * @meta.parser emapper\emapper
 * @author emaphp
 */
class ProductParameterMap {
	/**
	 * @map.property pcod
	 */
	public $code;
	
	/**
	 * @map.property price
	 */
	public $cost;
	
	/**
	 * @map.type boolean
	 */
	public $refurbished;
}
?>