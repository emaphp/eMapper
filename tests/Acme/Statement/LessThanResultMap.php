<?php
namespace Acme\Statement;

/**
 * @ResultMap
 */
class LessThanResultMap {
	/**
	 * @Column sale_id
	 */
	public $saleId;
	
	/**
	 * @Statement Product.priceNotLessThanEqual
	 * @Param 250
	 */
	public $notLessThanEqual;
	
	/**
	 * @Statement Product.priceLessThanEqual
	 * @Param 200
	 */
	public $lessThanEqual;
	
	/**
	 * @Statement Product.priceNotLessThan
	 * @Param 550.75
	 */
	public $notLessThan;
	
	/**
	 * @Statement Product.priceLessThan
	 * @Param 150.65
	 */
	public $lessThan;
}