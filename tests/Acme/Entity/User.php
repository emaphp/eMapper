<?php
namespace Acme\Entity;

/**
 * @parser emapper\emapper
 * @entity
 */
class User {
	/**
	 * @type int
	 */
	public $id;
	
	public $birthDate;
	
	/**
	 * @getter getName
	 * @setter setName
	 */
	private $name;
	
	public function setName($name) {
		$this->name = $name;
	}
	
	public function getName() {
		return $this->name;
	}
}
?>