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
	 * @Column product_id
	 */
	private $productId;
	
	/**
	 * @Column user_id
	 */
	private $userId;
	
	/**
	 * @ManyToOne Product
	 * @Attr(productId)
	 */
	private $product;
	
	/**
	 * @ManyToOne Acme\Association\User
	 * @Attr(userId)
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
	
	public function getProductId() {
		return $this->productId;
	}
	
	public function getUserId() {
		return $this->userId;
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