<?php
namespace Acme\Result\Attribute;

/**
 * @Entity users
 */
class User {
	public $user_name;
	
	/**
	 * @Id
	 * @Column user_id
	 * @Type int
	 */
	public $id;
	
	/**
	 * @Column birth_date
	 * @Type dt
	 */
	protected $birthDate;
	
	/**
	 * @Eval (strtoupper (#user_name))
	 * @Type string
	 */
	public $uppercase_name;
	
	/**
	 * @Eval (+ (%0) (%1))
	 * @Param(id)
	 * @Param 5
	 */
	public $fakeId;
	
	/**
	 * @Eval (->format (->diff (#birthDate) (now)) "%y")
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
