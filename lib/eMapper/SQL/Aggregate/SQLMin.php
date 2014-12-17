<?php
namespace eMapper\SQL\Aggregate;

/**
 * The SQLMin class representes the MIN SQL aggregate function.
 * @author emaphp
 */
class SQLMin extends SQLFunction {
	public function getName() {
		return 'MIN';
	}
	
	public function getDefaultType() {
		return 'float';
	}
}