<?php
namespace Acme\Result\Attribute;

/**
 * @parser emapper\emapper
 * @entity
 * @author emaphp
 */
class ExtraSale {
	/**
	 * @column product_id
	 * @type integer
	 */
	public $productId;
	
	/**
	 * @column user_id
	 * @var integer
	 */
	public $userId;
	
	/**
	 * @stmt getProduct
	 * @type array
	 */
	public $product;
	
	/**
	 * @stmt getUser
	 * @arg #userId
	 * @type obj
	 */
	public $user;
}
?>