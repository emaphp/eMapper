<?php
namespace Acme\Parameter;

/**
 * @ParameterMap
 */
class ProductParameterMap {
	/**
	 * @Attr pcod
	 */
	public $code;
	
	/**
	 * @Attr price
	 */
	public $cost;
	
	/**
	 * @Type boolean
	 */
	public $refurbished;
}
?>