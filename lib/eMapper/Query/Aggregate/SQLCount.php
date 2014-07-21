<?php
namespace eMapper\Query\Aggregate;

class SQLCount extends SQLFunction {
	public function __construct() {
	}
	
	public function getExpression(ClassProfile $profile) {
		return 'COUNT(*)';
	}
}
?>