<?php
namespace Acme\Reflection\Parameter;

class ExampleUser {
	public $name;
	public $lastname;
	private $password;
	
	public function __construct($name, $lastname, $password) {
		$this->name = $name;
		$this->lastname = $lastname;
		$this->password = $password;
	}
	
	public function getPassword() {
		return $this->password;
	}
}
?>