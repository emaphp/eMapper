<?php
namespace eMapper\Query\Aggregate;

use eMapper\Reflection\Profile\ClassProfile;

class SQLCount extends SQLFunction {
	public function __construct() {
	}
	
	public function getExpression(ClassProfile $profile) {
		return 'COUNT(*)';
	}
}
?>