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
class MapperBuilderTest extends \PHPUnit_Framework_TestCase {
	use SQLiteConfig;
	
	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testBuildException() {
		$config = [];
		$driver = SQLiteDriver::build($config);
	}
	
	public function testBuild() {
		$filename = $this->getFilename();
		$config = ['database' => $filename];
		$driver = SQLiteDriver::build($config);
	
		$this->assertInstanceOf('eMapper\Engine\SQLite\SQLiteDriver', $driver);
		$this->assertTrue($driver->hasOption('filename'));
		$this->assertEquals($filename, $driver->getOption('filename'));
	}
	
	public function testBuildFromConnection() {
		$conn = $this->getConnection();
		$driver = new SQLiteDriver($conn);
		$this->assertInstanceOf('eMapper\Engine\SQLite\SQLiteDriver', $driver);
	
		$mapper = new Mapper($driver);
		$two = $mapper->type('i')->query("SELECT 1 + 1");
		$this->assertEquals(2, $two);
	
		$row = $mapper->type('array')->query("SELECT * FROM users WHERE user_id = 1");
		$this->assertInternalType('array', $row);
	
		$row = $mapper->type('object')->query("SELECT * FROM products WHERE product_id = 1");
		$this->assertInstanceOf('stdClass', $row);
		$conn->close();
	}
}
?>