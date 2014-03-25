<?php
namespace Acme\Result;

/**
 * @parser emapper\emapper
 * @author emaphp
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