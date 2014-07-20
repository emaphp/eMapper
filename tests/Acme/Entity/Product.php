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
	 * @Column product_code
	 */
	public $code;

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