<?php
namespace eMapper\Mapper\ScalarMapper;

use eMapper\Mapper\AbstractMapperTest;

abstract class AbstractIntegerTypeTest extends AbstractMapperTest {
	public function testInteger() {
		$value = $this->mapper->type('integer')->query("SELECT 2");
		$this->assertEquals(2, $value);
	
		$value = $this->mapper->type('int')->query("SELECT user_id FROM users WHERE user_name = 'jkirk'");
		$this->assertEquals(3, $value);
	
		$value = $this->mapper->type('i')->query("SELECT * FROM users WHERE user_name = 'ishmael'");
		$this->assertEquals(5, $value);
	
		$result = $this->mapper->type('i')->query("SELECT birth_date FROM users WHERE user_name = 'jdoe'");
		$this->assertInternalType('integer', $result);
		$this->assertEquals(1987, $result);
	
		$result = $this->mapper->type('i')->query("SELECT last_login FROM users WHERE user_name = 'jdoe'");
		$this->assertInternalType('integer', $result);
		$this->assertEquals(2013, $result);
	
		$result = $this->mapper->type('i')->query("SELECT newsletter_time FROM users WHERE user_name = 'jdoe'");
		$this->assertInternalType('integer', $result);
		$this->assertEquals(12, $result);
	
		$result = $this->mapper->type('i')->query("SELECT price FROM products WHERE product_id = 1");
		$this->assertInternalType('integer', $result);
		$this->assertEquals(150, $result);
	
		$result = $this->mapper->type('i')->query("SELECT rating FROM products WHERE product_id = 1");
		$this->assertInternalType('integer', $result);
		$this->assertEquals(4, $result);
	
		$result = $this->mapper->type('i')->query("SELECT refurbished FROM products WHERE product_id = 1");
		$this->assertInternalType('integer', $result);
		$this->assertEquals(0, $result);
	
		$result = $this->mapper->type('i')->query("SELECT manufacture_year FROM products WHERE product_id = 1");
		$this->assertInternalType('integer', $result);
		$this->assertEquals(2011, $result);
	
		$result = $this->mapper->type('i')->query("SELECT discount FROM sales WHERE sale_id = 1");
		$this->assertInternalType('integer', $result);
		$this->assertEquals(0, $result);
	}
	
	public function testIntegerColumn() {
		$value = $this->mapper->type('integer', 'product_id')->query("SELECT * FROM sales WHERE sale_id = 1");
	
		$this->assertInternalType('integer', $value);
		$this->assertEquals(5, $value);
	}
	
	public function testIntegerList() {
		$values = $this->mapper->type('integer[]')->query("SELECT user_id FROM sales ORDER BY sale_id ASC");
	
		$this->assertInternalType('array', $values);
		$this->assertCount(4, $values);
	
		$this->assertEquals(1, $values[0]);
		$this->assertEquals(5, $values[1]);
		$this->assertEquals(2, $values[2]);
		$this->assertEquals(3, $values[3]);
	}
	
	public function testIntegerColumnList() {
		$values = $this->mapper->type('integer[]', 'user_id')->query("SELECT * FROM sales ORDER BY sale_id ASC");
	
		$this->assertInternalType('array', $values);
		$this->assertCount(4, $values);
	
		$this->assertEquals(1, $values[0]);
		$this->assertEquals(5, $values[1]);
		$this->assertEquals(2, $values[2]);
		$this->assertEquals(3, $values[3]);
	}
}
?>