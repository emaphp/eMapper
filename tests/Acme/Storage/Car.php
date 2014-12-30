<?php
namespace Acme\Storage;

/**
 * @Entity cars
 */
class Car {
	/**
	 * @Id
	 * @Type int
	 * @Column car_id
	 */
	public $id;
	
	/**
	 * @Type int
	 * @Column driver_id
	 * @Nullable
	 */
	public $driverId;
	
	/**
	 * @Type string
	 */
	public $brand;
	
	/**
	 * @Type string
	 */
	public $model;
	
	/**
	 * @ManyToOne Driver
	 * @Attr(driverId)
	 */
	public $driver;
}