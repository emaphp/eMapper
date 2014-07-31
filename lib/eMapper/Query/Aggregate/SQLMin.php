<?php
namespace eMapper\Query\Aggregate;

use eMapper\Reflection\Profile\ClassProfile;

/**
 * The SQLMin class represents the generic MIN aggregate function.
 * @author emaphp
 */
class SQLMin extends SQLFunction {
	public function getExpression(ClassProfile $profile) {
		return sprintf("MIN(%s)", $this->field->getColumnName($profile));
	}
}
?>