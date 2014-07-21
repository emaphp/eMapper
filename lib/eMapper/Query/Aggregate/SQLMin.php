<?php
namespace eMapper\Query\Aggregate;

use eMapper\Reflection\Profile\ClassProfile;

class SQLMin extends SQLFunction {
	public function getExpression(ClassProfile $profile) {
		return sprintf("MIN(%s)", $this->field->getColumnName($profile));
	}
}
?>