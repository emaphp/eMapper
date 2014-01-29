<?php
namespace Acme\Result\Attribute;

/**
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
	 * @xtype obj
	 */
	public $user;
}
?>