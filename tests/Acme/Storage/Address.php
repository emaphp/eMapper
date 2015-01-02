<?php
namespace Acme\Storage;

/**
 * @Entity addresses
 */
class Address {
	/**
	 * @Id
	 * @Type int
	 * @Column address_id
	 */
	public $id;
	
	/**
	 * @Type string
	 */
	public $city;
	
	/**
	 * @Type string
	 */
	public $street;
	
	/**
	 * @Type int
	 */
	public $number;
	
	/**
	 * @OneToOne Person
	 * @Attr addressId
	 * @Cascade
	 */
	public $person;
}
