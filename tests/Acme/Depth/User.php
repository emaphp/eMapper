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
	 * @StatementId "findBoughtProducts"
	 * @Parameter(id)
	 */
	public $products;
	
	/**
	 * @StatementId "totalBoughtProducts"
	 * @Parameter(id)
	 * @Scalar
	 */
	public $totalProducts;
}
?>