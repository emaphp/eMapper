<?php
namespace Acme\Storage;

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
	 * @Type string
	 */
	public $firstname;
	
	/**
	 * @Type string
	 */
	public $lastname;
	
	/**
	 * @Type string
	 */
	public $gender;
	
	/**
	 * @OneToOne User
	 * @Attr(userId)
	 */
	public $user;
}