<?php
namespace Acme\Result\Attribute;

/**
 * @Entity sales
 */
class ExtraSale {
	/**
	 * @Id
	 * @Column product_id
	 * @Type integer
	 */
	public $productId;
	
	/**
	 * @Column user_id
	 * @Type integer
	 */
	public $userId;
	
	/**
	 * @Statement Product.findByPk
	 * @Param(productId)
	 * @Type array
	 */
	public $product;
	
	/**
	 * @Statement User.findByPk
	 * @Param(userId)
	 * @Type obj
	 */
	public $user;
}
?>
