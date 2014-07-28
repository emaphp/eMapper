<?php
namespace Acme\Entity;

/**
 * @Entity products
 * @DefaultNamespace products
 */
class Product {
	/**
	 * @Id
	 * @Column product_id
	 * @Type int
	 */
	public $id;
	
	/**
	 * @Unique
	 * @Column product_code
	 */
	public $code;
	
	/**
	 * 
	 * @Type float
	 */
	public $price;

	protected $category;
	
	/**
	 * @Type Acme\RGBColor
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