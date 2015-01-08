<?php
namespace Acme\Statement;

/**
 * @Entity profiles
 */
class Profile {
	/**
	 * @Id
	 * @Type int
	 * @Column profile_id
	 */
	public $id;
	
	/**
	 * @Type int
	 * @Column user_id
	 */
	public $userId;
	
	/**
	 * @Statement User.findById
	 * @Param(userId)
	 */
	public $user;
}