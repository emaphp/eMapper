<?php
namespace eMapper\Fluent\Query\Clause;

use eMapper\SQL\Predicate\SQLPredicate;
use eMapper\SQL\Predicate\Filter;
use eMapper\Engine\Generic\Driver;
use eMapper\Query\Schema;

/**
 * The ArgumentClause class is an abstraction of a sql clause initialized through SQLPredicate instances
 * @author emaphp
 */
abstract class ArgumentClause {
	/**
	 * Conditional clause
	 * @var \eMapper\SQL\Predicate\SQLPredicate | string
	 */
	protected $clause;
	
	/**
	 * Connection driver
	 * @var \eMapper\Engine\Generic\Driver
	 */
	protected $driver;
	
	/**
	 * Additional arguments
	 * @var array
	 */
	protected $args = [];
	
	public function __construct(Driver $driver, $args) {
		$this->driver = $driver;
		
		if ($args[0] instanceof SQLPredicate)
			$this->clause = count($args) > 1 ? new Filter($args) : $args[0];
		elseif (is_string($args[0]) && !empty($args[0])) {
			$this->clause = array_shift($args);
			$this->args = $args;
		}
		else
			throw new \UnexpectedValueException($this->getName() . " clause must be defined as a SQLPredicate instance or a non-empty string");
	}
	
	/**
	 * Returns current clause name
	 */
	abstract function getName();
	
	/**
	 * Builds current clause
	 * @param \eMapper\Query\Schema $schema
	 * @param string alias
	 * @return string
	 */
	public function build(Schema &$schema, $alias = null) {
		if ($this->clause instanceof SQLPredicate) {
			if (!is_null($alias))
				$this->clause->setAlias($alias);
			return $this->clause->evaluate($this->driver, $schema);
		}
	
		return $this->clause;
	}
	
	public function getClause() {
		return $this->clause;
	}
	
	public function hasArguments() {
		return !empty($this->args);
	}
	
	public function getArguments() {
		return $this->args;
	}
}