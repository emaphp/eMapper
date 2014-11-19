<?php
namespace Acme\Depth;

/**
 * @Entity users
 */
class User {
	/**
	 * @Id
	 * @Column user_id
	 * @Type integer
	 */
	public $id;
	
	/**
	 * @Column user_name
	 * @Type string
	 */
	public $name;
	
	/**
	 * @Query "SELECT p.* FROM sales s INNER JOIN products p ON s.product_id = p.product_id WHERE s.user_id = %{i}"
	 * @Param(id)
	 * @Type obj:Acme\Depth\Product[]
	 */
	public $products;
	
	/**
	 * @Query "SELECT COUNT(*) FROM sales s INNER JOIN products p ON s.product_id = p.product_id WHERE s.user_id = %{i}"
	 * @Param(id)
	 * @Type int
	 * @Cacheable
	 */
	public $totalProducts;
}
?>