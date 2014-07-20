<?php
namespace Acme\Parameter;

/**
 * @ParameterMap
 */
class ProductParameterMap {
	/**
	 * @Property pcod
	 */
	public $code;
	
	/**
	 * @Property price
	 */
	public $cost;
	
	/**
	 * @Type boolean
	 */
	public $refurbished;
}
?>