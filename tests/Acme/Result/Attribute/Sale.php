<?php
namespace Acme\Result\Attribute;

/**
 * @Entity sales
 */
class Sale {
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
	 * @Query "SELECT * FROM products WHERE product_id = %{i}"
	 * @Param(productId)
	 * @Type obj
	 */
	public $product;
	
	/**
	 * @Query "SELECT * FROM users WHERE user_id = #{userId}"
	 * @Type obj:Acme\Result\Attribute\User
	 */
	public $user;
}
?>
