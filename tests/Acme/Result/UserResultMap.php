<?php
namespace Acme\Result;

/**
 * @meta.parser emapper\emapper
 * @author emaphp
 */
class UserResultMap {
	/**
	 * @map.type integer
	 */
	public $user_id;
	
	/**
	 * @map.column user_name
	 */
	public $name;
	
	/**
	 * @map.type string
	 * @map.column last_login
	 */
	public $lastLogin;
}
?>