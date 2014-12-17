<?php
namespace eMapper\SQL\Aggregate;

/**
 * The SQLSum class representes the SUM SQL aggregate function.
 * @author emaphp
 */
class SQLSum extends SQLFunction {
	public function getName() {
		return 'SUM';
	}
	
	public function getDefaultType() {
		return 'float';
	}
}