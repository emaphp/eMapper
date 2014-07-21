<?php
namespace eMapper\Query\Aggregate;

use eMapper\Reflection\Profile\ClassProfile;

class SQLMax extends SQLFunction {
	public function getExpression(ClassProfile $profile) {
		return sprintf("MAX(%s)", $this->field->getColumnName($profile));
	}
}
?>