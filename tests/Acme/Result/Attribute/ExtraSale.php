<?php
namespace Acme\Result\Attribute;

/**
 * @meta.parser emapper\emapper
 * @map.entity
 * @author emaphp
 */
class ExtraSale {
	/**
	 * @map.column product_id
	 * @map.type integer
	 */
	public $productId;
	
	/**
	 * @map.column user_id
	 * @var integer
	 */
	public $userId;
	
	/**
	 * @map.stmt getProduct
	 * @map.type array
	 */
	public $product;
	
	/**
	 * @map.stmt getUser
	 * @map.arg #userId
	 * @map.type obj
	 */
	public $user;
}
?>