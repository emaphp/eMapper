<?php
namespace Acme\Reflection\Parameter;

/**
 * @parser emapper\emapper
 * @entity
 * @author emaphp
 */
class UserEntity {
	public $name;
	public $surname;
	
	/**
	 * @setter setPassword
	 * @getter getPassword
	 * @var string
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