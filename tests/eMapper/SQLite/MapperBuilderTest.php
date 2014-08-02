<?php
namespace eMapper\SQLite;

use eMapper\Engine\SQLite\SQLiteDriver;
use eMapper\Mapper;
/**
 * Test creating instances of SQLiteMapper though different methods
 * @author emaphp
 * @group sqlite
 * @group builder
 */
class MapperBuilderTest extends SQLiteTest {
	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testBuildException() {
		$config = [];
		$driver = SQLiteDriver::build($config);
	}
	
	public function testBuild() {
		$config = ['database' => self::$filename];
		$driver = SQLiteDriver::build($config);
	
		$this->assertInstanceOf('eMapper\Engine\SQLite\SQLiteDriver', $driver);
		$this->assertTrue($driver->hasOption('db.filename'));
		$this->assertEquals(self::$filename, $driver->getOption('db.filename'));
	}
	
	public function testBuildFromConnection() {
		$driver = new SQLiteDriver(self::$conn);
		$this->assertInstanceOf('eMapper\Engine\SQLite\SQLiteDriver', $driver);
	
		$mapper = new Mapper($driver);
		$two = $mapper->type('i')->query("SELECT 1 + 1");
		$this->assertEquals(2, $two);
	
		$row = $mapper->type('array')->query("SELECT * FROM users WHERE user_id = 1");
		$this->assertInternalType('array', $row);
	
		$row = $mapper->type('object')->query("SELECT * FROM products WHERE product_id = 1");
		$this->assertInstanceOf('stdClass', $row);
	}
}
?>