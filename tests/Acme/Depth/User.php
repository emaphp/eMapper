<?php
namespace Acme\Depth;

/**
 * @meta.parser emapper\emapper
 * @map.entity
 * @author emaphp
 */
class User {
	/**
	 * @map.column user_id
	 * @var int
	 */
	public $id;
	
	/**
	 * @map.column user_name
	 * @var string
	 */
	public $name;
	
	/**
	 * @map.stmt "findBoughtProducts"
	 * @map.arg #id
	 */
	public $products;
	
	/**
	 * @map.stmt "totalBoughtProducts"
	 * @map.arg #id
	 */
	public $totalProducts;
}
?>