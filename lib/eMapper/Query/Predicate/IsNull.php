<?php
namespace eMapper\Query\Predicate;

use eMapper\Engine\Generic\Driver;
use eMapper\Reflection\Profile\ClassProfile;
use eMapper\Query\Field;
use eMapper\Query\SQL\ColumnTranslator;

/**
 * The IsNull class defines a predicate for NULL comparison values.
 * @author emaphp
 */
class IsNull extends SQLPredicate {
	public function render(Driver $driver) {
		if ($this->negate)
			return '%s IS NOT NULL';
		return '%s IS NULL';
	}
	
	public function evaluate(ColumnTranslator $translator, Driver $driver, \ArrayObject &$args, \ArrayObject &$joins = null, $arg_index = 0) {
		$column = $translator->translate($this->field, $joins, $this->alias);
		
		if ($this->negate)
			return "$column IS NOT NULL";
		return "$column IS NULL";
	}
}
?>