<?php
namespace Acme\Result\Attribute;

/**
 * @parser emapper\emapper
 * @entity
 */
class Good {
	/**
	 * @column product_id
	 * @var int
	 */
	public $id;
	
	public $price;
	
	/**
	 * @type boolean
	 */
	public $refurbished;
	
	/**
	 * @cond (#refurbished)
	 * @eval (<- "Special offer: 50% OFF!!!")
	 */
	public $specialDiscount;
}
?>