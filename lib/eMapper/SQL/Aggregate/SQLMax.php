<?php
namespace eMapper\SQL\Aggregate;

/**
 * The SQLMax class represents the MAX SQL aggregate function.
 * @author emaphp
 */
class SQLMax extends SQLFunction {
	public function getName() {
		return 'MAX';
	}
	
	public function getDefaultType() {
		return 'float';
	}
}