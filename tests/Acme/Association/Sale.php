<?php
namespace Acme\Association;

/**
 * @Entity sales
 */
class Sale {
	/**
	 * @Id
	 * @Column sale_id
	 */
	private $id;
	
	/**
	 * @ManyToOne Product
	 * @Column product_id
	 */
	private $product;
	
	/**
	 * @ManyToOne Acme\Association\User
	 * @Column user_id
	 * @Lazy
	 */
	private $user;
	
	/**
	 * @Type float
	 */
	private $discount;
	
	public function getId() {
		return $this->id;
	}
	
	public function getProduct() {
		return $this->product;
	}
	
	public function getUser() {
		return $this->user;
	}
	
	public function getDiscount() {
		return $this->discount;
	}
}
?>