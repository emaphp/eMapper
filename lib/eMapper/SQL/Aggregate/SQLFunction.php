<?php
namespace eMapper\SQL\Aggregate;

use eMapper\Reflection\ClassProfile;
use eMapper\Query\Attr;
use eMapper\Query\Field;
use eMapper\Query\Func;

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
	
	/**
	 * Obtains a Func instance for this object
	 * @throws \InvalidArgumentException
	 * @return \eMapper\Query\Func
	 */
	public function getFunctionInstance() {
		if (is_string($this->argument))
			$this->argument = new Attr($this->argument);
		if (!$this->argument instanceof Field)
			throw new \InvalidArgumentException(sprintf("Function '%s' expects an argument of type string or \eMapper\Query\Field", $this->getName()));
		return new Func($this->getName(), [$this->argument]);
	}
}
