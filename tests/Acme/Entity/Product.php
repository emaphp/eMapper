<?php
namespace Acme\Entity;

/**
 * @map.entity
 * @map.table products
 */
class Product {
	/**
	 * @map.pk
	 * @map.column product_id
	 * @var int
	 */
	public $id;
	
	/**
	 * @map.column product_code
	 */
	public $code;

	protected $category;
	
	/**
	 * @map.type Acme\RGBColor
	 */
	public $color;
	
	public function setCategory($category) {
		$this->category = $category;
	}
	
	public function getCategory() {
		return $this->category;
	}
}
?>