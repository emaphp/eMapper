<?php
namespace eMapper\Mapper\ScalarMapper;

use eMapper\Mapper\AbstractMapperTest;

abstract class AbstractFloatTypeTest extends AbstractMapperTest {
	public function testFloat() {
		$value = $this->mapper->type('float')->query("SELECT 2.5");
		$this->assertEquals(2.5, $value);
	
		$value = $this->mapper->type('double')->query("SELECT price FROM products WHERE product_id = 3");
		$this->assertEquals(70.9, $value);
	
		$value = $this->mapper->type('f', 'price')->query("SELECT * FROM products WHERE product_id = 5");
		$this->assertEquals(300.3, $value);
	
		$result = $this->mapper->type('f')->query("SELECT user_id FROM users WHERE user_name = 'jdoe'");
		$this->assertInternalType('float', $result);
		$this->assertEquals(1.0, $result);
	
		$result = $this->mapper->type('f')->query("SELECT user_name FROM users WHERE user_name = 'jdoe'");
		$this->assertInternalType('float', $result);
		$this->assertEquals(0, $result);
	
		$result = $this->mapper->type('f')->query("SELECT birth_date FROM users WHERE user_name = 'jdoe'");
		$this->assertInternalType('float', $result);
		$this->assertEquals(1987, $result);
	
		$result = $this->mapper->type('f')->query("SELECT last_login FROM users WHERE user_name = 'jdoe'");
		$this->assertInternalType('float', $result);
		$this->assertEquals(2013, $result);
	
		$result = $this->mapper->type('f')->query("SELECT newsletter_time FROM users WHERE user_name = 'jdoe'");
		$this->assertInternalType('float', $result);
		$this->assertEquals(12, $result);
	
		$result = $this->mapper->type('f')->query("SELECT avatar FROM users WHERE user_name = 'jdoe'");
		$this->assertInternalType('float', $result);
		$this->assertEquals(0, $result);
	
		$result = $this->mapper->type('f')->query("SELECT rating FROM products WHERE product_id = 1");
		$this->assertInternalType('float', $result);
		$this->assertEquals(4.5, $result);
	
		$result = $this->mapper->type('f')->query("SELECT refurbished FROM products WHERE product_id = 1");
		$this->assertInternalType('float', $result);
		$this->assertEquals(0, $result);
	
		$result = $this->mapper->type('f')->query("SELECT manufacture_year FROM products WHERE product_id = 1");
		$this->assertInternalType('float', $result);
		$this->assertEquals(2011, $result);
	
		$result = $this->mapper->type('f')->query("SELECT discount FROM sales WHERE sale_id = 1");
		$this->assertInternalType('float', $result);
		$this->assertEquals(0.25, $result);
	}
	
	public function testFloatColumn() {
		$value = $this->mapper->type('float', 'price')->query("SELECT * FROM products WHERE product_id = 1");
	
		$this->assertInternalType('float', $value);
		$this->assertEquals(150.65, $value);
	}
	
	public function testFloatList() {
		$values = $this->mapper->type('float[]')->query("SELECT rating FROM products ORDER BY product_id ASC");
	
		$this->assertInternalType('array', $values);
		$this->assertCount(8, $values);
	
		$this->assertEquals(4.5, $values[0]);
		$this->assertEquals(3.9, $values[1]);
		$this->assertEquals(4.1, $values[2]);
		$this->assertEquals(3.8, $values[3]);
		$this->assertEquals(4.8, $values[4]);
		$this->assertEquals(4.3, $values[5]);
		$this->assertEquals(4.7, $values[6]);
		$this->assertEquals(4.5, $values[7]);
	}
	
	public function testFloatColumnList() {
		$values = $this->mapper->type('float[]', 'discount')->query("SELECT * FROM sales ORDER BY sale_id ASC");
	
		$this->assertInternalType('array', $values);
		$this->assertCount(4, $values);
	
		$this->assertEquals(.25, $values[0]);
		$this->assertEquals(.15, $values[1]);
		$this->assertEquals(.12, $values[2]);
		$this->assertEquals(.1, $values[3]);
	}
}
?>