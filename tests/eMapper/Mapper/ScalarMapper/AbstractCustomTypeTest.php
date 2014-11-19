<?php
namespace eMapper\Mapper\ScalarMapper;

use eMapper\MapperTest;

abstract class AbstractCustomTypeTest extends MapperTest {
	public function testCustomType() {
		$value = $this->mapper->type('Acme\RGBColor')->query("SELECT 'FF00ff'");
		$this->assertInstanceOf('Acme\RGBColor', $value);
		$this->assertEquals(255, $value->red);
		$this->assertEquals(0, $value->green);
		$this->assertEquals(255, $value->blue);
	
		$value = $this->mapper->type('color')->query("SELECT color FROM products WHERE product_id = 1");
		$this->assertInstanceOf('Acme\RGBColor', $value);
		$this->assertEquals(225, $value->red);
		$this->assertEquals(26, $value->green);
		$this->assertEquals(26, $value->blue);
	}
	
	public function testCustomTypeColumn() {
		$value = $this->mapper->type('Acme\RGBColor', 'color')->query("SELECT * FROM products WHERE product_id = 1");
	
		$this->assertInstanceOf('Acme\RGBColor', $value);
		$this->assertEquals(225, $value->red);
		$this->assertEquals(26, $value->green);
		$this->assertEquals(26, $value->blue);
	}
	
	public function testCustomTypeList() {
		$values = $this->mapper->type('Acme\RGBColor[]')->query("SELECT color FROM products ORDER BY product_id ASC");
	
		$this->assertInternalType('array', $values);
		$this->assertCount(8, $values);
	
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
		
		$this->assertNull($values[5]);
		
		$this->assertInstanceOf('Acme\RGBColor', $values[6]);
		$this->assertEquals(255, $values[6]->red);
		$this->assertEquals(255, $values[6]->green);
		$this->assertEquals(255, $values[6]->blue);
		
		$this->assertNull($values[7]);
	}
	
	public function testCustomTypeColumnList() {
		$values = $this->mapper->type('Acme\RGBColor[]', 'color')->query("SELECT * FROM products ORDER BY product_id ASC");
	
		$this->assertInternalType('array', $values);
		$this->assertCount(8, $values);
	
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
		
		$this->assertNull($values[5]);
		
		$this->assertInstanceOf('Acme\RGBColor', $values[6]);
		$this->assertEquals(255, $values[6]->red);
		$this->assertEquals(255, $values[6]->green);
		$this->assertEquals(255, $values[6]->blue);
		
		$this->assertNull($values[7]);
	}
}
?>