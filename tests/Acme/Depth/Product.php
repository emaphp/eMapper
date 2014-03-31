<?php
namespace Acme\Depth;

/**
 * @meta.parser emapper\emapper
 * @map.entity
 * @author emaphp
 */
class Product {
	/**
	 * @map.column product_id
	 * @var int
	 */
	public $id;
	
	/**
	 * @map.column product_code
	 * @var string
	 */
	public $code;
	
	public $category;
	
	public $price;
	
	/**
	 * @map.stmt "findRelatedProducts"
	 * @map.arg #category
	 * @map.arg #id
	 */
	public $related;
}
?>