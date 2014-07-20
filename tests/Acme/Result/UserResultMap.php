<?php
namespace Acme\Result;

/**
 * @ResultMap
 */
class UserResultMap {
	/**
	 * @Id
	 * @Type integer
	 */
	public $user_id;
	
	/**
	 * @Column user_name
	 */
	public $name;
	
	/**
	 * @Type string
	 * @Column last_login
	 */
	public $lastLogin;
}
?>