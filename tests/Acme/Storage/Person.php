<?php
namespace Acme\Storage;

/**
 * @Entity people
 */
class Person {
	/**
	 * @Id
	 * @Type int
	 * @Column person_id
	 */
	public $id;
	
	/**
	 * @Type int
	 * @Column address_id
	 * @Nullable
	 */
	public $addressId;
	
	/**
	 * @Type string
	 */
	public $name;
	
	/**
	 * @Type string
	 */
	public $lastname;
	
	/**
	 * @OneToOne Address
	 * @Attr(addressId)
	 */
	public $address;
}
