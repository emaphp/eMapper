<?php
namespace Acme\Reflection\Parameter;

/**
 * @meta.parser emapper\emapper
 * @author emaphp
 */
class UserArrayParameterMap {
	/**
	 * @map.type str
	 */
	public $name;
	
	/**
	 * @map.property lastname
	 * @var string
	 */
	public $surname;
}
?>