<?php
namespace Acme\Statement;

/**
 * @ResultMap
 */
class GreaterThanResultMap {
	/**
	 * @Column sale_id
	 */
	public $saleId;
	
	/**
	 * @Statement Product.priceGreaterThan
	 * @Param 250
	 */
	public $greaterThan;
	
	/**
	 * @Statement Product.priceNotGreaterThan
	 * @Param 200
	 */
	public $notGreaterThan;
	
	/**
	 * @Statement Product.priceGreaterThanEqual
	 * @Param 550.75
	 */
	public $greaterThanEqual;
	
	/**
	 * @Statement Product.priceNotGreaterThanEqual
	 * @Param 150.65
	 */
	public $notGreaterThanEqual;
}
