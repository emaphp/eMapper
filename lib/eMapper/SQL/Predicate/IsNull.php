<?php
namespace eMapper\SQL\Predicate;

use eMapper\Engine\Generic\Driver;
use eMapper\Query\Field;
use eMapper\SQL\Field\FieldTranslator;

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
	
	public function evaluate(FieldTranslator $translator, Driver $driver, array &$args, array &$joins = null, $arg_index = 0) {
		$column = $translator->translate($this->field, $joins, $this->alias);
		
		if ($this->negate)
			return "$column IS NOT NULL";
		return "$column IS NULL";
	}
}
?>