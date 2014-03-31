<?php
namespace Acme\Entity;

/**
 * @meta.parser emapper\emapper
 * @map.entity
 */
class User {
	/**
	 * @map.type int
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