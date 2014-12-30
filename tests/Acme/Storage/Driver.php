<?php
namespace Acme\Storage;

/**
 * @Entity drivers
 */
class Driver {
	/**
	 * @Id
	 * @Type int
	 * @Column driver_id
	 */
	public $id;
	
	/**
	 * @Type string
	 */
	public $name;
	
	/**
	 * @Type string
	 * @Column birth_date
	 */
	public $birthDate;
	
	/**
	 * @OneToMany Car
	 * @Attr driverId
	 */
	public $cars;
}
