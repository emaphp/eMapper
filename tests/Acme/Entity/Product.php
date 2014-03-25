<?php
namespace Acme\Entity;

/**
 * @parser emapper\emapper
 * @entity
 */
class Product {
	/**
	 * @column product_id
	 * @var int
	 */
	public $id;
	
	/**
	 * @column product_code
	 */
	public $code;
	
	/**
	 * @setter setCategory
	 * @getter getCategory
	 */
	protected $category;
	
	/**
	 * @type Acme\RGBColor
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