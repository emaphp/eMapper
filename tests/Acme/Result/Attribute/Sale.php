<?php
namespace Acme\Result\Attribute;

/**
 * @parser emapper\emapper
 * @entity
 */
class Sale {
	/**
	 * @column product_id
	 * @var integer
	 */
	public $productId;
	
	/**
	 * @column user_id
	 * @var integer
	 */
	public $userId;
	
	/**
	 * @query "SELECT * FROM products WHERE product_id = %{i}"
	 * @arg #productId
	 * @type obj
	 */
	public $product;
	
	/**
	 * @query "SELECT * FROM users WHERE user_id = #{userId}"
	 * @type obj:Acme\Result\Attribute\User
	 */
	public $user;
}
?>