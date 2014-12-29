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
	 * @Column user_name
	 * @Unique
	 */
	public $name;
	
	/**
	 * @Type string
	 * @Column birth_date
	 */
	public $birthDate;
	
	/**
	 * @Type timestamp
	 * @Column last_login
	 */
	public $lastLogin;
	
	/**
	 * @Type string
	 * @Column newsletter_time
	 */
	public $notify;
}