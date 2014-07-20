<?php
namespace Acme\Result\Attribute;

/**
 * @Entity products
 */
class Good {
	/**
	 * @Id
	 * @Column product_id
	 * @Type int
	 */
	public $id;
	
	public $price;
	
	/**
	 * @Type boolean
	 */
	public $refurbished;
	
	/**
	 * @If (#refurbished)
	 * @Eval (<- "Special offer: 50% OFF!!!")
	 */
	public $specialDiscount;
}
?>