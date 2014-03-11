<?php
namespace eMapper\SQLite;

use eMapper\Engine\SQLite\SQLiteMapper;
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
		$config = array();
		$mapper = SQLiteMapper::build($config);
	}
	
	public function testBuild() {
		$config = array('database' => self::$filename);
		$mapper = SQLiteMapper::build($config);
	
		$this->assertInstanceOf('eMapper\Engine\SQLite\SQLiteMapper', $mapper);
		$this->assertArrayHasKey('db.filename', $mapper->config);
		$this->assertEquals(self::$filename, $mapper->config['db.filename']);
	}
	
	public function testBuildADditionalConfig() {
		$config = array('database' => self::$filename);
		$additional_config = array('custom.option' => 100);
	
		$mapper = SQLiteMapper::build($config, $additional_config);
		$this->assertInstanceOf('eMapper\Engine\SQLite\SQLiteMapper', $mapper);
		$this->assertArrayHasKey('db.filename', $mapper->config);
		$this->assertEquals(self::$filename, $mapper->config['db.filename']);
		$this->assertArrayHasKey('custom.option', $mapper->config);
		$this->assertEquals(100, $mapper->config['custom.option']);
	}
	
	public function testBuildFromConnection() {
		$mapper = new SQLiteMapper(self::$conn);
		$this->assertInstanceOf('eMapper\Engine\SQLite\SQLiteMapper', $mapper);
	
		$two = $mapper->type('i')->query("SELECT 1 + 1");
		$this->assertEquals(2, $two);
	
		$row = $mapper->type('array')->query("SELECT * FROM users WHERE user_id = 1");
		$this->assertInternalType('array', $row);
	
		$row = $mapper->type('object')->query("SELECT * FROM products WHERE product_id = 1");
		$this->assertInstanceOf('stdClass', $row);
	}
}

?>