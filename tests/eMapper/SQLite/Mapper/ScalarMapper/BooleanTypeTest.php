<?php
namespace eMapper\SQLite\Mapper\ScalarMapper;

use eMapper\SQLite\SQLiteConfig;
use eMapper\Mapper\ScalarMapper\AbstractBooleanTypeTest;

/**
 * Test SQLiteMapper with boolean values
 * @author emaphp
 * @group sqlite
 * @group mapper
 * @group boolean
 */
class BooleanTypeTest extends AbstractBooleanTypeTest {
	use SQLiteConfig;
	
	public function testBoolean() {
		$result = $this->mapper->type('boolean')->query("SELECT NULL");
		$this->assertNull($result);
		
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
}
?>