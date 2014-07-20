<?php
namespace Acme\Entity;

/**
 * @Entity users
 */
class User {
	/**
	 * @Id
	 * @Type integer
	 */
	public $id;
	
	public $birthDate;
	
	private $name;
	
	public function setName($name) {
		$this->name = $name;
	}
	
	public function getName() {
		return $this->name;
	}
}
?>