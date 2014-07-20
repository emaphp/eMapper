<?php
namespace Acme\Depth;

/**
 * @Entity products
 */
class Product {
	/**
	 * @Id
	 * @Column product_id
	 * @Type integer
	 */
	public $id;
	
	/**
	 * @Column product_code
	 * @Type string
	 */
	public $code;
	
	public $category;
	
	public $price;
	
	/**
	 * @StatementId "findRelatedProducts"
	 * @Parameter(category)
	 * @Parameter(id)
	 */
	public $related;
}
?>