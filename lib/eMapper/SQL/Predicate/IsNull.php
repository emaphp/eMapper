<?php
namespace eMapper\SQL\Predicate;

use eMapper\Engine\Generic\Driver;
use eMapper\Query\Field;
use eMapper\Query\Schema;

/**
 * The IsNull class defines a predicate for NULL comparison values.
 * @author emaphp
 */
class IsNull extends SQLPredicate {
	public function generate(Driver $driver) {
		return $this->negate ? '%s IS NOT NULL' : '%s IS NULL';
	}
	
	public function evaluate(Driver $driver, Schema &$schema) {
		$column = $schema->translate($this->field, $this->alias);
		return $this->negate ? "$column IS NOT NULL" : "$column IS NULL";
	}
}