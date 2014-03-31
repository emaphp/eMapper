<?php
namespace Acme\Entity;

/**
 * @meta.parser emapper\emapper
 * @color red
 * @speed fast
 */
class Car extends Vehicle {
	/**
	 * @has 4
	 */
	public $wheels;
	
	/**
	 * @full 4
	 */
	public $capacity;
}
?>