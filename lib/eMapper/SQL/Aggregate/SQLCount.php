<?php
namespace eMapper\SQL\Aggregate;

use eMapper\Query\Column;

/**
 * The SQLCount class represents the COUNT SQL aggregate function.
 * @author emaphp
 */
class SQLCount extends SQLFunction {
	public function __construct() {
	}
	
	public function getName() {
		return 'COUNT';
	}
	
	public function getDefaultType() {
		return 'int';
	}
	
	public function getArgument() {
		return new Column('*');
	}
}