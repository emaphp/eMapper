<?php
namespace eMapper\PostgreSQL\Mapper\ScalarMapper;

use eMapper\PostgreSQL\PostgreSQLTest;
use eMapper\Engine\PostgreSQL\PostgreSQLDriver;
use eMapper\Mapper;
use Acme\Type\RGBColorTypeHandler;

/**
 * Tests Mapper with custom type values
 * @author emaphp
 * @group postgre
 * @group mapper
 * @group custom
 */
class CustomTypeTest extends PostgreSQLTest {
	public $pgsql;
	
	public function __construct() {
		parent::__construct();
	
		$driver = new PostgreSQLDriver(pg_connect(self::$connstring));
		$this->pgsql = new Mapper($driver);
		$this->pgsql->addType('Acme\RGBColor', new RGBColorTypeHandler(), 'color');
	}
	
	public function testCustomType() {
		$value = $this->pgsql->type('Acme\RGBColor')->query("SELECT 'FF00ff'");
		$this->assertInstanceOf('Acme\RGBColor', $value);
		$this->assertEquals(255, $value->red);
		$this->assertEquals(0, $value->green);
		$this->assertEquals(255, $value->blue);
	
		$value = $this->pgsql->type('color')->query("SELECT color FROM products WHERE product_id = 1");
		$this->assertInstanceOf('Acme\RGBColor', $value);
		$this->assertEquals(225, $value->red);
		$this->assertEquals(26, $value->green);
		$this->assertEquals(26, $value->blue);
	}
	
	public function testCustomTypeColumn() {
		$value = $this->pgsql->type('Acme\RGBColor', 'color')->query("SELECT * FROM products WHERE product_id = 1");
	
		$this->assertInstanceOf('Acme\RGBColor', $value);
		$this->assertEquals(225, $value->red);
		$this->assertEquals(26, $value->green);
		$this->assertEquals(26, $value->blue);
	}
	
	public function testCustomTypeList() {
		$values = $this->pgsql->type('Acme\RGBColor[]')->query("SELECT color FROM products ORDER BY product_id ASC");
	
		$this->assertInternalType('array', $values);
		$this->assertCount(5, $values);
	
		$this->assertInstanceOf('Acme\RGBColor', $values[0]);
		$this->assertEquals(225, $values[0]->red);
		$this->assertEquals(26, $values[0]->green);
		$this->assertEquals(26, $values[0]->blue);
	
		$this->assertInstanceOf('Acme\RGBColor', $values[1]);
		$this->assertEquals(12, $values[1]->red);
		$this->assertEquals(27, $values[1]->green);
		$this->assertEquals(217, $values[1]->blue);
	
		$this->assertInstanceOf('Acme\RGBColor', $values[2]);
		$this->assertEquals(112, $values[2]->red);
		$this->assertEquals(124, $values[2]->green);
		$this->assertEquals(4, $values[2]->blue);
	
		$this->assertNull($values[3]);
	
		$this->assertInstanceOf('Acme\RGBColor', $values[4]);
		$this->assertEquals(0, $values[4]->red);
		$this->assertEquals(167, $values[4]->green);
		$this->assertEquals(235, $values[4]->blue);
	}
	
	public function testCustomTypeColumnList() {
		$values = $this->pgsql->type('Acme\RGBColor[]', 'color')->query("SELECT * FROM products ORDER BY product_id ASC");
	
		$this->assertInternalType('array', $values);
		$this->assertCount(5, $values);
	
		$this->assertInstanceOf('Acme\RGBColor', $values[0]);
		$this->assertEquals(225, $values[0]->red);
		$this->assertEquals(26, $values[0]->green);
		$this->assertEquals(26, $values[0]->blue);
	
		$this->assertInstanceOf('Acme\RGBColor', $values[1]);
		$this->assertEquals(12, $values[1]->red);
		$this->assertEquals(27, $values[1]->green);
		$this->assertEquals(217, $values[1]->blue);
	
		$this->assertInstanceOf('Acme\RGBColor', $values[2]);
		$this->assertEquals(112, $values[2]->red);
		$this->assertEquals(124, $values[2]->green);
		$this->assertEquals(4, $values[2]->blue);
	
		$this->assertNull($values[3]);
	
		$this->assertInstanceOf('Acme\RGBColor', $values[4]);
		$this->assertEquals(0, $values[4]->red);
		$this->assertEquals(167, $values[4]->green);
		$this->assertEquals(235, $values[4]->blue);
	}
}

?>