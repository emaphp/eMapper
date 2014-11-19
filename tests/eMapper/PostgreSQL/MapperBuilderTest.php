<?php
namespace eMapper\PostgreSQL;

use eMapper\Engine\PostgreSQL\PostgreSQLDriver;
use eMapper\Mapper;

/**
 * Test building PostgreSQLDriver intances
 *
 * @author emaphp
 * @group postgre
 * @group builder
 */
class MapperBuilderTest extends \PHPUnit_Framework_TestCase {
	use PostgreSQLConfig;
	
	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testBuildException() {
		$config = [];
		$driver = PostgreSQLDriver::build($config);
	}
	
	public function testArrayBuild() {
		$config = ['database' => 'emapper_testing', 'host' => 'localhost', 'port' => '5432', 'username' => 'postgres', 'password' => 'c4lpurn14'];
		$driver = PostgreSQLDriver::build($config);
		$this->assertInstanceOf('eMapper\Engine\PostgreSQL\PostgreSQLDriver', $driver);
		
		$mapper = new Mapper($driver);
		$two  = $mapper->type('i')->query("SELECT 1 + 1");
		$this->assertEquals(2, $two);
		
		$row = $mapper->type('array')->query("SELECT * FROM users WHERE user_id = 1");
		$this->assertInternalType('array', $row);
		
		$row = $mapper->type('object')->query("SELECT * FROM products WHERE product_id = 1");
		$this->assertInstanceOf('stdClass', $row);
		$driver->close();
	}
	
	public function testBuildFromConnection() {
		$conn = $this->getConnection();
		$driver = new PostgreSQLDriver($conn);
		$this->assertInstanceOf('eMapper\Engine\PostgreSQL\PostgreSQLDriver', $driver);
		
		$mapper = new Mapper($driver);
		$two  = $mapper->type('i')->query("SELECT 1 + 1");
		$this->assertEquals(2, $two);
		
		$row = $mapper->type('array')->query("SELECT * FROM users WHERE user_id = 1");
		$this->assertInternalType('array', $row);
		
		$row = $mapper->type('object')->query("SELECT * FROM products WHERE product_id = 1");
		$this->assertInstanceOf('stdClass', $row);
		pg_close($conn);
	}
}
?>