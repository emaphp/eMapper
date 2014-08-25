<?php
namespace Acme\Association;

/**
 * @Entity profiles
 */
class Profile {
	/**
	 * @Id
	 * @Column profile_id
	 */
	private $id;
	
	private $name;
	
	private $surname;
	
	private $gender;
	
	/**
	 * @Column user_id
	 */
	private $userId;
	
	/**
	 * @OneToOne User
	 * @Attr(userId)
	 */
	private $user;
	
	public function getId() {
		return $this->id;
	}
	
	public function getName() {
		return $this->name;
	}
	
	public function getSurname() {
		return $this->name;
	}
	
	public function getGender() {
		return $this->gender;
	}
	
	public function getUserId() {
		return $this->userId;
	}
	
	public function getUser() {
		return $this->user;
	}
}
?>