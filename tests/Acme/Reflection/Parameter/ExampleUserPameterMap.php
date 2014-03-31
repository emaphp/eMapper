<?php
namespace Acme\Reflection\Parameter;

/**
 * @meta.parser emapper\emapper
 * @author emaphp
 */
class ExampleUserPameterMap {
	public $name;
	
	/**
	 * @map.property lastname
	 */
	public $surname;
	
	/**
	 * @map.property password
	 */
	public $pass;
}
?>