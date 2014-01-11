<?php
namespace Acme\Result;


/**
 * @defaultClass stdClass
 */
class UserResultMap {
	/**
	 * @type integer
	 */
	public $user_id;
	
	/**
	 * @column user_name
	 */
	public $name;
	
	/**
	 * @type string
	 * @column last_login
	 */
	public $lastLogin;
}
?>