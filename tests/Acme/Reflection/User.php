<?php
namespace Acme\Reflection;

/**
 * @entity
 */
class User {
	/**
	 * @column user_id
	 * @var int
	 */
	public $id;
	
	/**
	 * @var string
	 */
	public $name;
	
	/**
	 * @var string
	 */
	public $surname;
	
	/**
	 * @eval (. (#surname) ', ' (#name))
	 * @var string
	 */
	public $fullName;
	
	/**
	 * @stmt profiles.findByUserId
	 * @arg #id
	 * @arg 3
	 */
	public $profiles;
	
	/**
	 * @eval (+ (count (#profiles)) (%0))
	 * @arg-self
	 * @arg 1
	 */
	public $totalProfiles;
	
	/**
	 * @query "SELECT last_login FROM login WHERE user_id = %{i}"
	 * @arg #id
	 * @option map.type dt
	 * @option custom 100
	 */
	public $lastConnection;
	
	/**
	 * @query "SELECT link FROM favorites WHERE user_id = #{id} AND confirmed = %{bool}"
	 * @arg-self
	 * @arg true
	 * @option map.type string[]
	 */
	public $favorites;
}
?>