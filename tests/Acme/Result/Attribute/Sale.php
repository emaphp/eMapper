<?php
namespace Acme\Result\Attribute;

/**
 * @meta.parser emapper\emapper
 * @map.entity
 */
class Sale {
	/**
	 * @map.column product_id
	 * @var integer
	 */
	public $productId;
	
	/**
	 * @map.column user_id
	 * @var integer
	 */
	public $userId;
	
	/**
	 * @map.query "SELECT * FROM products WHERE product_id = %{i}"
	 * @map.arg #productId
	 * @map.type obj
	 */
	public $product;
	
	/**
	 * @map.query "SELECT * FROM users WHERE user_id = #{userId}"
	 * @map.type obj:Acme\Result\Attribute\User
	 */
	public $user;
}
?>