<?php
namespace Acme\Storage;

/**
 * @Entity clients
 */
class Client {
	/**
	 * @Id
	 * @Type int
	 * @Column client_id
	 */
	public $id;
	
	/**
	 * @Type string
	 */
	public $firstname;
	
	/**
	 * @Type string
	 */
	public $lastname;
	
	/**
	 * @OneToMany Pet
	 * @Attr clientId
	 * @Index name
	 * @Cascade
	 */
	public $pets;
}