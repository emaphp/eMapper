<?php
namespace Acme\Depth;

/**
 * @entity
 * @author emaphp
 *
 */
class Product {
	/**
	 * @column product_id
	 * @var int
	 */
	public $id;
	
	/**
	 * @column product_code
	 * @var string
	 */
	public $code;
	
	public $category;
	
	public $price;
	
	/**
	 * @stmt "findRelatedProducts"
	 * @arg #category
	 * @arg #id
	 */
	public $related;
}
?>