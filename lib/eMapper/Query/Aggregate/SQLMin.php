<?php
namespace eMapper\Query\Aggregate;

use eMapper\Reflection\Profile\ClassProfile;

/**
 * The SQLMin class represents the generic MIN aggregate function.
 * @author emaphp
 */
class SQLMin extends SQLFunction {
	public function getExpression(ClassProfile $profile, $alias = '') {
		return sprintf("MIN(%s)", empty($alias) ? $this->field->getColumnName($profile) : $alias . '.' . $this->field->getColumnName($profile));
	}
}
?>