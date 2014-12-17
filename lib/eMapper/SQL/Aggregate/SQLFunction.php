<?php
namespace eMapper\SQL\Aggregate;

use eMapper\Reflection\ClassProfile;

/**
 * The SQLFunction class represents a SQL aggregate function.
 * @author emaphp
 */
abstract class SQLFunction {
	/**
	 * Function argument
	 * @var mixed
	 */
	protected $argument;
	
	public function __construct($argument) {
		$this->argument = $argument;
	}
	
	/**
	 * Obtains function name
	 */
	abstract function getName();
	
	/**
	 * Obtains default mapping type for this function
	 */
	abstract function getDefaultType();
	
	/**
	 * Returns function argument
	 * @return mixed
	 */
	public function getArgument() {
		return $this->argument;
	}
}
