<?php
namespace Acme\Statement;

/**
 * @Entity sales
 */
class Sale {
	/**
	 * @Id
	 * @Type int
	 * @Column sale_id
	 */
	public $id;
	
	/**
	 * @Type int
	 * @Column product_id
	 */
	public $productId;
	
	/**
	 * @Statement Product.idEquals
	 * @Param(productId)
	 */
	public $product;
	
	/**
	 * @Statement Product.idNotEquals
	 * @Param(productId)
	 */
	public $otherProducts;
}