<?php
namespace Acme\Reflection\Parameter;

/**
 * @meta.parser emapper\emapper
 * @map.entity
 * @author emaphp
 */
class UserEntity {
	public $name;
	public $surname;
	
	/**
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