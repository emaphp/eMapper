<?php
namespace Acme\Result\Attribute;

/**
 * @Entity sales
 * @author emaphp
 */
class SQLiteSale {
	/**
	 * @Id
	 * @Column product_id
	 * @Type integer
	 */
	public $productId;
	
	/**
	 * @Column user_id
	 * @Type integer
	 */
	public $userId;
	
	/**
	 * @Statement User.findByPk
	 * @Param(userId)
	 * @Type obj
	 */
	public $user;
}