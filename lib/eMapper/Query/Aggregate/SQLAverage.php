<?php
namespace eMapper\Query\Aggregate;

use eMapper\Reflection\Profile\ClassProfile;

class SQLAverage extends SQLFunction {
	public function getExpression(ClassProfile $profile) {
		return sprintf("AVG(%s)", $this->field->getColumnName($profile));
	}
}
?>