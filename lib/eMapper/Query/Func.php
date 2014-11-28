<?php
namespace eMapper\Query;

use eMapper\Reflection\Profile\ClassProfile;
use eMapper\Type\ToString;

/**
 * The Func class is aimed to build aggregate function expressions
 * @author emaphp
 */
abstract class Func extends Field {
	use ToString;
	
	/**
	 * Function arguments
	 * @var array
	 */
	protected $arguments;
	
	public function __callstatic($method, $args = null) {
		$this->name = $method;
		$this->arguments = is_null($args) ? [] : $args;
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
		
		return $this->name . '(' . implode(',', $args) . ')';
	}
}
?>