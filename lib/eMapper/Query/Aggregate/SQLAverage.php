<?php
namespace eMapper\Query\Aggregate;

use eMapper\Reflection\Profile\ClassProfile;

/**
 * The SQLAverage class represents the generic AVG aggregate function.
 * @author emaphp
 */
class SQLAverage extends SQLFunction {
	public function getExpression(ClassProfile $profile, $alias = '') {
		return sprintf("AVG(%s)", empty($alias) ? $this->field->getColumnName($profile) : $alias . '.' . $this->field->getColumnName($profile));
	}
}
?>