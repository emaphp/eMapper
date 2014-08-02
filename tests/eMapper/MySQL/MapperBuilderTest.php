<?php
namespace eMapper\MySQL;

use eMapper\Engine\MySQL\MySQLDriver;
use eMapper\Mapper;

/**
 * Test building MySQLDriver intances
 * 
 * @author emaphp
 * @group mysql
 * @group builder
 */
class MapperBuilderTest extends MySQLTest {
	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testBuildException() {
		$config = [];
		$driver = MySQLDriver::build($config);
	}
	
	public function testBuild() {
		$config = ['database' => self::$config['database']];
		$driver = MySQLDriver::build($config);
		
		$this->assertInstanceOf('eMapper\Engine\MySQL\MySQLDriver', $driver);
		$this->assertTrue($driver->hasOption('db.name'));
		$this->assertEquals(self::$config['database'], $driver->getOption('db.name'));
	}
	
	public function testBuildFromConnection() {
		$driver = new MySQLDriver(self::$conn);
		$this->assertInstanceOf('eMapper\Engine\MySQL\MySQLDriver', $driver);
		
		$mapper = new Mapper($driver);
		$two  = $mapper->type('i')->query("SELECT 1 + 1");
		$this->assertEquals(2, $two);
		
		$row = $mapper->type('array')->query("SELECT * FROM users WHERE user_id = 1");
		$this->assertInternalType('array', $row);
		
		$row = $mapper->type('object')->query("SELECT * FROM products WHERE product_id = 1");
		$this->assertInstanceOf('stdClass', $row);
	}
}
?>