<?php
namespace Acme\Result\Attribute;

/**
 * @entity
 */
class User {
	public $user_name;
	
	/**
	 * @column user_id
	 * @var int
	 */
	public $id;
	
	/**
	 * @column birth_date
	 * @setter setBirthDate
	 * @getter getBirthDate
	 * @type dt
	 */
	protected $birthDate;
	
	/**
	 * @eval (strtoupper (#user_name))
	 * @var string
	 */
	public $uppercase_name;
	
	/**
	 * @eval (+ (%0) (%1))
	 * @arg #id
	 * @arg 5
	 */
	public $fakeId;
	
	/**
	 * @eval (->format (->diff (#birthDate) (now)) "%y")
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