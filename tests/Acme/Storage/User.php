<?php
namespace Acme\Storage;

/**
 * @Entity users
 */
class User {
	/**
	 * @Id
	 * @Type int
	 * @Column user_id
	 */
	public $id;
	
	/**
	 * @Type string
	 * @Unique
	 * @OnDuplicate update
	 */
	public $name;
	
	/**
	 * @Type string
	 */
	public $email;
	
	/**
	 * @Type timestamp
	 * @Column last_login
	 */
	public $lastLogin;
	
	/**
	 * @OneToOne Profile
	 * @Attr userId
	 * @Cascade
	 */
	public $profile;
}