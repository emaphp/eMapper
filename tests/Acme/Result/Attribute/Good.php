<?php
namespace Acme\Result\Attribute;

/**
 * @meta.parser emapper\emapper
 * @map.entity
 */
class Good {
	/**
	 * @map.column product_id
	 * @var int
	 */
	public $id;
	
	public $price;
	
	/**
	 * @map.type boolean
	 */
	public $refurbished;
	
	/**
	 * @map.cond (#refurbished)
	 * @map.eval (<- "Special offer: 50% OFF!!!")
	 */
	public $specialDiscount;
}
?>