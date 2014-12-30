<?php
namespace Acme\Storage;

/**
 * @Entity pets
 */
class Pet {
	/**
	 * @Id
	 * @Type int
	 * @Column pet_id
	 */
	public $id;
	
	/**
	 * @Type int
	 * @Column client_id
	 */
	public $clientId;
	
	/**
	 * @Type string
	 */
	public $name;
	
	/**
	 * @Type string
	 */
	public $type;
	
	/**
	 * @MamyToOne Client
	 * @Attr(clientId)
	 */
	public $owner;
}