<?php
namespace Acme\Entity;

/**
 * @meta.parser emapper\emapper
 * @map.entity
 */
class Product {
	/**
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