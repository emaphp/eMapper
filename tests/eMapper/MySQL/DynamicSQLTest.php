<?php
namespace eMapper\MySQL;

use eMapper\Engine\MySQL\Statement\MySQLStatement;
use eMapper\Engine\MySQL\Type\MySQLTypeManager;
use eMapper\AbstractDynamicSQLTest;
use eMapper\Mapper;
use eMapper\Engine\MySQL\MySQLDriver;
use Acme\Type\RGBColorTypeHandler;

/**
 * Test dynamic sql expressions
 * 
 * @author emaphp
 * @group dynamic
 * @group mysql
 */
class DynamicSQLTest extends AbstractDynamicSQLTest {
	use MySQLConfig;
	
	public function buildStatement() {
		$conn = new \mysqli($this->config['host'], $this->config['user'], $this->config['password'], $this->config['database']);
		$this->statement = new MySQLStatement($conn, new MySQLTypeManager(), null);
	}
	
	public function buildMapper() {
		$this->mapper = new Mapper(new MySQLDriver($this->config['database'], $this->config['host'], $this->config['user'], $this->config['password']));
		$this->mapper->addType('Acme\RGBColor', new RGBColorTypeHandler(), 'color');
	}
}
?>