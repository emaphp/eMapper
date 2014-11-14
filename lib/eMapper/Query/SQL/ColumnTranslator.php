<?php
namespace eMapper\Query\SQL;

use eMapper\Query\Field;

interface ColumnTranslator {
	function translate(Field $field, \ArrayObject $joins = null, $alias = null);
}
?>