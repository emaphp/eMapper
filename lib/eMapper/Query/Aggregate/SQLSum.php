<?php
namespace eMapper\Query\Aggregate;

use eMapper\Reflection\Profile\ClassProfile;

/**
 * The SQLSum class represents the generic SUM aggregate function.
 * @author emaphp
 */
class SQLSum extends SQLFunction {
	public function getExpression(ClassProfile $profile, $alias = '') {
		return sprintf("SUM(%s)", empty($alias) ? $this->field->getColumnName($profile) : $alias . '.' . $this->field->getColumnName($profile));
	}
}
?>