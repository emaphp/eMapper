<?php
namespace Acme\Reflection;

/**
 * @Entity users
 */
class User {
	/**
	 * @Id
	 * @Column user_id
	 * @Type int
	 */
	public $id;
	
	/**
	 * @Type string
	 */
	public $name;
	
	/**
	 * @Type string
	 */
	public $surname;
	
	/**
	 * @Eval (. (#surname) ', ' (#name))
	 * @Type string
	 */
	public $fullName;
	
	/**
	 * @Statement Profile.findByUserId
	 * @Param(id)
	 */
	public $profiles;
	
	/**
	 * @Eval (+ (count (#profiles)) (%0))
	 * @Param(self)
	 * @Param 1
	 */
	public $totalProfiles;
	
	/**
	 * @Query "SELECT last_login FROM login WHERE user_id = %{i}"
	 * @Param(id)
	 * @Type dt
	 * @Option(map.result) Acme\Reflection\ConnectionResultMap
	 * @Cacheable
	 */
	public $lastConnection;
	
	/**
	 * @Query "SELECT link FROM favorites WHERE user_id = #{id} AND confirmed = %{bool}"
	 * @Param(self)
	 * @Param true
	 * @Type string[]
	 */
	public $favorites;
}
?>
