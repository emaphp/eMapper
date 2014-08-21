<?php
namespace eMapper\Mapper\ScalarMapper;

use eMapper\Mapper\AbstractMapperTest;

abstract class AbstractBooleanTypeTest extends AbstractMapperTest {
	public function testBoolean() {
		$result = $this->mapper->type('boolean')->query("SELECT NULL");
		$this->assertNull($result);
	
		$value = $this->mapper->type('boolean')->query("SELECT TRUE");
		$this->assertTrue($value);
	
		$value = $this->mapper->type('bool')->query("SELECT FALSE");
		$this->assertFalse($value);
	
		$value = $this->mapper->type('b')->query("SELECT refurbished FROM products WHERE product_id = 1");
		$this->assertFalse($value);
	
		$value = $this->mapper->type('boolean')->query("SELECT * FROM products WHERE product_id = 5");
		$this->assertTrue($value);
	
		$result = $this->mapper->type('bool')->query("SELECT 'T'");
		$this->assertInternalType('boolean', $result);
		$this->assertTrue($result);
	
		$result = $this->mapper->type('bool')->query("SELECT 'F'");
		$this->assertInternalType('boolean', $result);
		$this->assertFalse($result);
	
		$result = $this->mapper->type('b')->query("SELECT 't'");
		$this->assertInternalType('boolean', $result);
		$this->assertTrue($result);
	
		$result = $this->mapper->type('b')->query("SELECT 'f'");
		$this->assertInternalType('boolean', $result);
		$this->assertFalse($result);
	
		$result = $this->mapper->type('b')->query("SELECT 'true'");
		$this->assertInternalType('boolean', $result);
		$this->assertTrue($result);
	
		$result = $this->mapper->type('b')->query("SELECT 'false'");
		$this->assertInternalType('boolean', $result);
		$this->assertFalse($result);
	
		$result = $this->mapper->type('b')->query("SELECT 'TRUE'");
		$this->assertInternalType('boolean', $result);
		$this->assertTrue($result);
	
		$result = $this->mapper->type('b')->query("SELECT 'FALSE'");
		$this->assertInternalType('boolean', $result);
		$this->assertFalse($result);
	
		$result = $this->mapper->type('b')->query("SELECT 0");
		$this->assertInternalType('boolean', $result);
		$this->assertFalse($result);
	
		$result = $this->mapper->type('b')->query("SELECT 1");
		$this->assertInternalType('boolean', $result);
		$this->assertTrue($result);
	
		$result = $this->mapper->type('b')->query("SELECT user_id FROM users WHERE user_name = 'jdoe'");
		$this->assertInternalType('boolean', $result);
		$this->assertTrue($result);
	
		$result = $this->mapper->type('b')->query("SELECT user_name FROM users WHERE user_name = 'jdoe'");
		$this->assertInternalType('boolean', $result);
		$this->assertTrue($result);
	
		$result = $this->mapper->type('b')->query("SELECT birth_date FROM users WHERE user_name = 'jdoe'");
		$this->assertInternalType('boolean', $result);
		$this->assertTrue($result);
	
		$result = $this->mapper->type('b')->query("SELECT last_login FROM users WHERE user_name = 'jdoe'");
		$this->assertInternalType('boolean', $result);
		$this->assertTrue($result);
	
		$result = $this->mapper->type('b')->query("SELECT newsletter_time FROM users WHERE user_name = 'jdoe'");
		$this->assertInternalType('boolean', $result);
		$this->assertTrue($result);
	
		$result = $this->mapper->type('b')->query("SELECT avatar FROM users WHERE user_name = 'jdoe'");
		$this->assertInternalType('boolean', $result);
		$this->assertTrue($result);
	
		$result = $this->mapper->type('b')->query("SELECT price FROM products WHERE product_id = 1");
		$this->assertInternalType('boolean', $result);
		$this->assertTrue($result);
	
		$result = $this->mapper->type('b')->query("SELECT rating FROM products WHERE product_id = 1");
		$this->assertInternalType('boolean', $result);
		$this->assertTrue($result);
	
		$result = $this->mapper->type('b')->query("SELECT manufacture_year FROM products WHERE product_id = 1");
		$this->assertInternalType('boolean', $result);
		$this->assertTrue($result);
	
		$result = $this->mapper->type('b')->query("SELECT discount FROM sales WHERE sale_id = 1");
		$this->assertInternalType('boolean', $result);
		$this->assertTrue($result);
	}
	
	public function testBooleanColumn() {
		$value = $this->mapper->type('bool', 'refurbished')->query("SELECT * FROM products WHERE product_id = 1");
	
		$this->assertInternalType('boolean', $value);
		$this->assertFalse($value);
	}
	
	public function testBooleanList() {
		$values = $this->mapper->type('bool[]')->query("SELECT refurbished FROM products ORDER BY product_id ASC");
	
		$this->assertInternalType('array', $values);
		$this->assertCount(8, $values);
	
		$this->assertFalse($values[0]);
		$this->assertFalse($values[1]);
		$this->assertFalse($values[2]);
		$this->assertFalse($values[3]);
		$this->assertTrue($values[4]);
		$this->assertFalse($values[5]);
		$this->assertFalse($values[6]);
		$this->assertFalse($values[7]);
	}
	
	public function testBooleanColumnList() {
		$values = $this->mapper->type('bool[]', 'refurbished')->query("SELECT * FROM products ORDER BY product_id ASC");
	
		$this->assertInternalType('array', $values);
		$this->assertCount(8, $values);
	
		$this->assertFalse($values[0]);
		$this->assertFalse($values[1]);
		$this->assertFalse($values[2]);
		$this->assertFalse($values[3]);
		$this->assertTrue($values[4]);
		$this->assertFalse($values[5]);
		$this->assertFalse($values[6]);
		$this->assertFalse($values[7]);
	}
}
?>