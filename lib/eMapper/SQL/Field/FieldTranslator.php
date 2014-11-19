<?php
namespace eMapper\SQL\Field;

use eMapper\Query\Field;

interface FieldTranslator {
	/**
	 * Translates a field reference to the corresponding column
	 * @param Field $field
	 * @param array $joins
	 * @param string $alias
	 */
	function translate(Field $field, array &$joins = null, $alias = null);
}
?>