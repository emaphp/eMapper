<?php
namespace eMapper\SQL\Aggregate;

/**
 * The SQLAverage class represents the AVG SQL aggregate function.
 * @author emaphp
 */
class SQLAverage extends SQLFunction {
	public function getName() {
		return 'AVG';
	}
	
	public function getDefaultType() {
		return 'float';
	}
}