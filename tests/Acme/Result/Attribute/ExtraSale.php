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
	 * @StatementId getProduct
	 * @Type array
	 */
	public $product;
	
	/**
	 * @StatementId getUser
	 * @Parameter(userId)
	 * @Type obj
	 */
	public $user;
}
?>