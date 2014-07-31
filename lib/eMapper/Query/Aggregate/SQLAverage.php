<?php
namespace eMapper\Query\Aggregate;

use eMapper\Reflection\Profile\ClassProfile;

/**
 * The SQLAverage class represents the generic AVG aggregate function.
 * @author emaphp
 */
class SQLAverage extends SQLFunction {
	public function getExpression(ClassProfile $profile) {
		return sprintf("AVG(%s)", $this->field->getColumnName($profile));
	}
}
?>