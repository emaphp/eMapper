<?php
namespace eMapper\MySQL;

use eMapper\Engine\MySQL\MySQLMapper;

/**
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
		$config = array();
		$mapper = MySQLMapper::build($config);
	}
	
	public function testBuild() {
		$config = array('database' => self::$config['database']);
		$mapper = MySQLMapper::build($config);
		
		$this->assertInstanceOf('eMapper\Engine\MySQL\MySQLMapper', $mapper);
		$this->assertArrayHasKey('db.name', $mapper->config);
		$this->assertEquals(self::$config['database'], $mapper->config['db.name']);
	}
	
	public function testBuildADditionalConfig() {
		$config = array('database' => self::$config['database']);
		$additional_config = array('custom.option' => 100);
		
		$mapper = MySQLMapper::build($config, $additional_config);
		$this->assertInstanceOf('eMapper\Engine\MySQL\MySQLMapper', $mapper);
		$this->assertArrayHasKey('db.name', $mapper->config);
		$this->assertEquals(self::$config['database'], $mapper->config['db.name']);
		$this->assertArrayHasKey('custom.option', $mapper->config);
		$this->assertEquals(100, $mapper->config['custom.option']);
	}
	
	public function testBuildFromConnection() {
		$mapper = new MySQLMapper(self::$conn);
		$this->assertInstanceOf('eMapper\Engine\MySQL\MySQLMapper', $mapper);
		
		$two  = $mapper->type('i')->query("SELECT 1 + 1");
		$this->assertEquals(2, $two);
		
		$row = $mapper->type('array')->query("SELECT * FROM users WHERE user_id = 1");
		$this->assertInternalType('array', $row);
		
		$row = $mapper->type('object')->query("SELECT * FROM products WHERE product_id = 1");
		$this->assertInstanceOf('stdClass', $row);
	}
}
?>