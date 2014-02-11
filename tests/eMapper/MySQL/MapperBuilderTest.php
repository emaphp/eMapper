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
}
?>