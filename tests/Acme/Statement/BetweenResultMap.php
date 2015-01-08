<?php
namespace Acme\Statement;

/**
 * @ResultMap
 */
class BetweenResultMap {
	/**
	 * @Column sale_id
	 */
	public $saleId;
	
	/**
	 * @Statement Product.priceBetween
	 * @Param 150
	 * @Param 300
	 */
	public $between;
	
	/**
	 * @Statement Product.priceNotBetween
	 * @Param 150
	 * @Param 300
	 */
	public $notBetween;
}