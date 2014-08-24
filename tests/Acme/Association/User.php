<?php
namespace Acme\Association;

/**
 * @Entity users
 */
class User {
	/**
	 * @Id
	 * @Column user_id
	 */
	private $id;
	
	/**
	 * @Type string
	 * @Column user_name
	 */
	private $name;
	
	/**
	 * @Type blob
	 */
	private $avatar;
	
	/**
	 * @ManyToMany Product
	 * @JoinWith(favorites) prd_id
	 * @Column usr_id
	 * @Lazy
	 */
	private $favorites;
	
	/**
	 * @OneToOne Profile
	 * @Column user_id
	 * @Lazy
	 */
	private $profile;
	
	public function getId() {
		return $this->id;
	}
	
	public function getName() {
		return $this->name;
	}
	
	public function getAvatar() {
		return $this->avatar;
	}
	
	public function getFavorites() {
		return $this->favorites;
	}
	
	public function getProfile() {
		return $this->profile;
	}
}
?>