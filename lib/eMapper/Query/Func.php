<?php
namespace eMapper\Query;

use eMapper\Reflection\Profile\ClassProfile;
use eMapper\Type\ToString;

/**
 * The Func class is aimed to build aggregate function expressions
 * @author emaphp
 */
class Func extends Field {
	use ToString;
	
	/**
	 * Function arguments
	 * @var array
	 */
	protected $arguments;
	
	public function __construct($name, $args) {
		$this->name = $name;
		$this->arguments = $args;
	}
	
	public static function __callstatic($method, $args = null) {
		return new static($method, is_null($args) ? [] : $args);
	}
	
	public function getColumnName(ClassProfile $profile) {
		$args = [];
		
		foreach ($this->arguments as $arg) {
			if ($arg instanceof Field)
				$args[] = $arg->getColumnName($profile);
			else {
				$arg = $this->toString($arg);
				$args[] = $this->toString(is_null($arg) ? 'NULL' : $arg);
			}
		}
		
		return !empty($this->columnAlias) ? $this->name . '(' . implode(',', $args) . ') AS ' . $this->columnAlias : $this->name . '(' . implode(',', $args) . ')';
	}

	/**
	 * Obtains function arguments
	 * @return array
	 */
	public function getArguments() {
		return $this->arguments;
	}
}