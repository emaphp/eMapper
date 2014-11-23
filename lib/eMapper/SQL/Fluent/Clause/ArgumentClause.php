<?php
namespace eMapper\SQL\Fluent\Clause;

use eMapper\SQL\Predicate\SQLPredicate;
use eMapper\SQL\Predicate\Filter;
use eMapper\SQL\Field\FieldTranslator;
use eMapper\Engine\Generic\Driver;

abstract class ArgumentClause {
	/**
	 * Conditional clause
	 * @var mixed
	 */
	protected $clause;
	
	/**
	 * Additional arguments
	 * @var array
	 */
	protected $args = [];
	
	public function __construct($args) {
		if ($args[0] instanceof SQLPredicate)
			$this->clause = count($args) > 1 ? new Filter($args) : $args[0];
		elseif (is_string($args[0]) && !empty($args[0])) {
			$this->clause = array_shift($args);
			$this->args = $args;
		}
		else
			throw new \UnexpectedValueException($this->getName() . " clause must be defined as a SQLPredicate instance or a non-empty string");
	}
	
	abstract function getName();
	
	public function build(FieldTranslator $translator, Driver $driver) {
		if ($this->clause instanceof SQLPredicate) {
			$joins = [];
			$sql = $this->clause->evaluate($translator, $driver, $this->args, $joins, 0);
			return $sql;
		}
	
		return $this->clause;
	}
	
	public function getClause() {
		return $this->clause;
	}
	
	public function getArguments() {
		return $this->args;
	}
}