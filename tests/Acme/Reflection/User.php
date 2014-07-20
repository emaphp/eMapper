<?php
namespace Acme\Reflection;

/**
 * @Entity
 */
class User {
	/**
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
	 * @StatementId profiles.findByUserId
	 * @Parameter(id)
	 * @Parameter 3
	 */
	public $profiles;
	
	/**
	 * @Eval (+ (count (#profiles)) (%0))
	 * @Self
	 * @Parameter 1
	 */
	public $totalProfiles;
	
	/**
	 * @Query "SELECT last_login FROM login WHERE user_id = %{i}"
	 * @Parameter(id)
	 * @Type dt
	 * @Option(map.result) Acme\Reflection\ConnectionResultMap
	 */
	public $lastConnection;
	
	/**
	 * @Query "SELECT link FROM favorites WHERE user_id = #{id} AND confirmed = %{bool}"
	 * @Self
	 * @Parameter true
	 * @Type string[]
	 */
	public $favorites;
}
?>