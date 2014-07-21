<?php
namespace eMapper\Query\Aggregate;

use eMapper\Reflection\Profile\ClassProfile;

class SQLSum extends SQLFunction {
	public function getExpression(ClassProfile $profile) {
		return sprintf("SUM(%s)", $this->field->getColumnName($profile));
	}
}
?>