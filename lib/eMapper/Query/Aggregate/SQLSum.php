<?php
namespace eMapper\Query\Aggregate;

use eMapper\Reflection\Profile\ClassProfile;

/**
 * The SQLSum class represents the generic SUM aggregate function.
 * @author emaphp
 */
class SQLSum extends SQLFunction {
	public function getExpression(ClassProfile $profile) {
		return sprintf("SUM(%s)", $this->field->getColumnName($profile));
	}
}
?>