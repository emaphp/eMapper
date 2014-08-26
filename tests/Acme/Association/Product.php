<?php
namespace Acme\Association;

/**
 * @Entity products
 */
class Product {
	/**
	 * @Id
	 * @Column product_id
	 */
	private $id;
	
	/**
	 * @Column product_code
	 */
	private $code;
	
	/**
	 * @OneToMany Sale
	 * @Attr productId
	 * @Lazy
	 */
	private $sales;
	
	public function getId() {
		return $this->id;
	}
	
	public function getCode() {
		return $this->code;
	}
	
	public function getSales() {
		return $this->sales;
	}
}
?>