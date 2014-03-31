<?php
namespace Acme\Entity;

/**
 * @meta.parser custom
 * @moves forward
 * @speed slow
 */
abstract class Vehicle {
	/**
	 * @requires fuel
	 */
	public $engine;
	
	/**
	 * @measure passengers
	 * @full 2
	 */
	public $capacity;
}
?>