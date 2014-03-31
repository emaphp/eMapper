<?php
namespace Acme\Result\Attribute;

/**
 * @meta.parser emapper\emapper
 * @map.entity
 */
class User {
	public $user_name;
	
	/**
	 * @map.column user_id
	 * @var int
	 */
	public $id;
	
	/**
	 * @map.column birth_date
	 * @map.type dt
	 */
	protected $birthDate;
	
	/**
	 * @map.eval (strtoupper (#user_name))
	 * @var string
	 */
	public $uppercase_name;
	
	/**
	 * @map.eval (+ (%0) (%1))
	 * @map.arg #id
	 * @map.arg 5
	 */
	public $fakeId;
	
	/**
	 * @map.eval (->format (->diff (#birthDate) (now)) "%y")
	 */
	public $age;
	
	public function setBirthDate($birthDate) {
		$this->birthDate = $birthDate;
	}
	
	public function getBirthDate() {
		return $this->birthDate;
	}
}
?>