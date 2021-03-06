<?php
namespace Acme\Reflection\Parameter;

/**
 * @Entity users
 */
class UserEntity {
	/**
	 * @Id
	 */
	public $user_id;
	public $name;
	public $surname;
	
	/**
	 * @Type string
	 */
	private $password;
	
	public function setPassword($password) {
		$this->password = $password;
	}
	
	public function getPassword() {
		return $this->password;
	}
}
?>