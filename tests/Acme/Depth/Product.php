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
	 * @Query "SELECT * FROM products WHERE category = %{s} AND product_id <> %{i} ORDER BY product_id ASC"
	 * @Param(category)
	 * @Param(id)
	 * @Type obj:Acme\Depth\Product[id]
	 */
	public $related;
}
?>
