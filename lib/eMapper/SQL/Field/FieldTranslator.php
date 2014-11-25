<?php
namespace eMapper\SQL\Field;

use eMapper\Query\Field;

interface FieldTranslator {
	/**
	 * Translates a field reference to the corresponding column
	 * @param Field $field
	 * @param string $alias
	 * @param array $joins
	 */
	function translate(Field $field, $alias, &$joins = null);
}
?>