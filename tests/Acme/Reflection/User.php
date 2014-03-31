<?php
namespace Acme\Reflection;

/**
 * @meta.parser emapper\emapper
 * @map.entity
 */
class User {
	/**
	 * @map.column user_id
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
	 * @map.eval (. (#surname) ', ' (#name))
	 * @var string
	 */
	public $fullName;
	
	/**
	 * @map.stmt profiles.findByUserId
	 * @map.arg #id
	 * @map.arg 3
	 */
	public $profiles;
	
	/**
	 * @map.eval (+ (count (#profiles)) (%0))
	 * @map.self-arg
	 * @map.arg 1
	 */
	public $totalProfiles;
	
	/**
	 * @map.query "SELECT last_login FROM login WHERE user_id = %{i}"
	 * @map.arg #id
	 * @map.type dt
	 */
	public $lastConnection;
	
	/**
	 * @map.query "SELECT link FROM favorites WHERE user_id = #{id} AND confirmed = %{bool}"
	 * @map.self-arg
	 * @map.arg true
	 * @map.type string[]
	 */
	public $favorites;
}
?>