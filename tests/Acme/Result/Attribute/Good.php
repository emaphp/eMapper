<?php
namespace Acme\Result\Attribute;

/**
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