<?php
namespace Acme\Depth;

/**
 * @parser emapper\emapper
 * @entity
 * @author emaphp
 */
class User {
	/**
	 * @column user_id
	 * @var int
	 */
	public $id;
	
	/**
	 * @column user_name
	 * @var string
	 */
	public $name;
	
	/**
	 * @stmt "findBoughtProducts"
	 * @arg #id
	 */
	public $products;
	
	/**
	 * @stmt "totalBoughtProducts"
	 * @arg #id
	 */
	public $totalProducts;
}
?>