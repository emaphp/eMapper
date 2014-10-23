<?php
namespace eMapper\SQLite;

use eMapper\AbstractStatementTest;

/**
 * Test SQLiteStatement class
 * @author emaphp
 * @group sqlite
 * @group statement
 */
class StatementTest extends AbstractStatementTest {
	use SQLiteConfig;
	
	/**
	 * Tests boolean type handler for mixed values
	 */
	public function testBoolean() {
		$result = $this->statement->format('CONN_%{boolean}', ['1'], []);
		$this->assertEquals('CONN_1', $result);
	
		$result = $this->statement->format('CONN_%{bool}', ['0'], []);
		$this->assertEquals('CONN_0', $result);
	
		$result = $this->statement->format('CONN_%{boolean}', ['T'], []);
		$this->assertEquals('CONN_1', $result);
	
		$result = $this->statement->format('CONN_%{bool}', ['F'], []);
		$this->assertEquals('CONN_0', $result);
	
		$result = $this->statement->format('CONN_%{boolean}', ['t'], []);
		$this->assertEquals('CONN_1', $result);
	
		$result = $this->statement->format('CONN_%{bool}', ['f'], []);
		$this->assertEquals('CONN_0', $result);
	
		$result = $this->statement->format('CONN_%{b}', [''], []);
		$this->assertEquals('CONN_0', $result);
	
		$result = $this->statement->format('CONN_%{b}', [6], []);
		$this->assertEquals('CONN_1', $result);
	
		$result = $this->statement->format('CONN_%{b}', [0], []);
		$this->assertEquals('CONN_0', $result);
	
		$result = $this->statement->format('CONN_%{b}', [3.65], []);
		$this->assertEquals('CONN_1', $result);
	
		$result = $this->statement->format('CONN_%{b}', [0.0], []);
		$this->assertEquals('CONN_0', $result);
	
		$result = $this->statement->format('CONN_%{b}', [true], []);
		$this->assertEquals('CONN_1', $result);
	
		$result = $this->statement->format('CONN_%{b}', [false], []);
		$this->assertEquals('CONN_0', $result);
	}
	
	/**
	 * Tests boolean type handler for array values
	 */
	public function testBooleanArray() {
		$result = $this->statement->format('CONN_%{b}', [[false, true, false]], []);
		$this->assertEquals('CONN_0,1,0', $result);
	
		$result = $this->statement->format('CONN_%{boolean}', [['1', '0', '', 'F', 't', 'f', 'T']], []);
		$this->assertEquals('CONN_1,0,0,0,1,0,1', $result);
	
		$result = $this->statement->format('CONN_%{boolean}', [[10, 1, 0, 0.0, 3.65]], []);
		$this->assertEquals('CONN_1,1,0,0,1', $result);
	}
	
	/**
	 * Tests property replacements for string values
	 */
	public function testStringPropertyReplace() {
		//as array
		$result = $this->statement->format('PROD_%{0[code]}', [['code' => 'XYZ123']], []);
		$this->assertEquals("PROD_'XYZ123'", $result);
	
		$result = $this->statement->format('PROD_#{code:s}', [['code' => 'XYZ123']], []);
		$this->assertEquals("PROD_'XYZ123'", $result);
	
		$result = $this->statement->format('PROD_#{code:ss}', [['code' => 'XYZ123']], []);
		$this->assertEquals('PROD_XYZ123', $result);
	
		$result = $this->statement->format('PROD_#{code:i}', [['code' => 'XYZ123']], []);
		$this->assertEquals('PROD_0', $result);
	
		$result = $this->statement->format('PROD_#{code:f}', [['code' => 'XYZ123']], []);
		$this->assertEquals('PROD_0', $result);
	
		$result = $this->statement->format('PROD_#{code:b}', [['code' => 'XYZ123']], []);
		$this->assertEquals('PROD_1', $result);
	
		$result = $this->statement->format('PROD_#{code:null}', [['code' => 'XYZ123']], []);
		$this->assertEquals('PROD_NULL', $result);
	
		//as object
		$prod = new \stdClass();
		$prod->code = 'XYZ123';
	
		$result = $this->statement->format('PROD_%{0[code]}', [$prod], []);
		$this->assertEquals("PROD_'XYZ123'", $result);
	
		$result = $this->statement->format('PROD_#{code:s}', [$prod], []);
		$this->assertEquals("PROD_'XYZ123'", $result);
	
		$result = $this->statement->format('PROD_#{code:ss}', [$prod], []);
		$this->assertEquals('PROD_XYZ123', $result);
	
		$result = $this->statement->format('PROD_#{code:i}', [$prod], []);
		$this->assertEquals('PROD_0', $result);
	
		$result = $this->statement->format('PROD_#{code:f}', [$prod], []);
		$this->assertEquals('PROD_0', $result);
	
		$result = $this->statement->format('PROD_#{code:b}', [$prod], []);
		$this->assertEquals('PROD_1', $result);
	
		$result = $this->statement->format('PROD_#{code:null}', [$prod], []);
		$this->assertEquals('PROD_NULL', $result);
	}
	
	/**
	 * Tests property replacements for integer values
	 */
	public function testIntegerPropertyReplace() {
		//as array
		$result = $this->statement->format('PROD_%{0[id]}', [['id' => 42]], []);
		$this->assertEquals('PROD_42', $result);
	
		$result = $this->statement->format('PROD_#{id:s}', [['id' => 42]], []);
		$this->assertEquals("PROD_'42'", $result);
	
		$result = $this->statement->format('PROD_#{id:ss}', [['id' => 42]], []);
		$this->assertEquals('PROD_42', $result);
	
		$result = $this->statement->format('PROD_#{id:i}', [['id' => 42]], []);
		$this->assertEquals('PROD_42', $result);
	
		$result = $this->statement->format('PROD_#{id:f}', [['id' => 42]], []);
		$this->assertEquals('PROD_42', $result);
	
		$result = $this->statement->format('PROD_#{id:b}', [['id' => 42]], []);
		$this->assertEquals('PROD_1', $result);
	
		$result = $this->statement->format('PROD_#{id:null}', [['id' => 42]], []);
		$this->assertEquals('PROD_NULL', $result);
	
		//as object
		$prod = new \stdClass();
		$prod->id = 42;
	
		$result = $this->statement->format('PROD_%{0[id]}', [$prod], []);
		$this->assertEquals('PROD_42', $result);
	
		$result = $this->statement->format('PROD_#{id:s}', [$prod], []);
		$this->assertEquals("PROD_'42'", $result);
	
		$result = $this->statement->format('PROD_#{id:ss}', [$prod], []);
		$this->assertEquals('PROD_42', $result);
	
		$result = $this->statement->format('PROD_#{id:i}', [$prod], []);
		$this->assertEquals('PROD_42', $result);
	
		$result = $this->statement->format('PROD_#{id:f}', [$prod], []);
		$this->assertEquals('PROD_42', $result);
	
		$result = $this->statement->format('PROD_#{id:b}', [$prod], []);
		$this->assertEquals('PROD_1', $result);
	
		$result = $this->statement->format('PROD_#{id:null}', [$prod], []);
		$this->assertEquals('PROD_NULL', $result);
	}
	
	/**
	 * Tests property replacements for double values
	 */
	public function testFloatPropertyReplace() {
		//as array
		$result = $this->statement->format('PROD_%{0[price]}', [['price' => 39.95]], []);
		$this->assertEquals('PROD_39.95', $result);
	
		$result = $this->statement->format('PROD_#{price:s}', [['price' => 39.95]], []);
		$this->assertEquals("PROD_'39.95'", $result);
	
		$result = $this->statement->format('PROD_#{price:ss}', [['price' => 39.95]], []);
		$this->assertEquals('PROD_39.95', $result);
	
		$result = $this->statement->format('PROD_#{price:i}', [['price' => 39.95]], []);
		$this->assertEquals('PROD_39', $result);
	
		$result = $this->statement->format('PROD_#{price:f}', [['price' => 39.95]], []);
		$this->assertEquals('PROD_39.95', $result);
	
		$result = $this->statement->format('PROD_#{price:b}', [['price' => 39.95]], []);
		$this->assertEquals('PROD_1', $result);
	
		$result = $this->statement->format('PROD_#{price:null}', [['price' => 39.95]], []);
		$this->assertEquals('PROD_NULL', $result);
	
		//as object
		$prod = new \stdClass();
		$prod->price = 39.95;
	
		$result = $this->statement->format('PROD_%{0[price]}', [$prod], []);
		$this->assertEquals('PROD_39.95', $result);
	
		$result = $this->statement->format('PROD_#{price:s}', [$prod], []);
		$this->assertEquals("PROD_'39.95'", $result);
	
		$result = $this->statement->format('PROD_#{price:ss}', [$prod], []);
		$this->assertEquals('PROD_39.95', $result);
	
		$result = $this->statement->format('PROD_#{price:i}', [$prod], []);
		$this->assertEquals('PROD_39', $result);
	
		$result = $this->statement->format('PROD_#{price:f}', [$prod], []);
		$this->assertEquals('PROD_39.95', $result);
	
		$result = $this->statement->format('PROD_#{price:b}', [$prod], []);
		$this->assertEquals('PROD_1', $result);
	
		$result = $this->statement->format('PROD_#{price:null}', [$prod], []);
		$this->assertEquals('PROD_NULL', $result);
	}
	
	/**
	 * Tests property replacements for boolean values
	 */
	public function testBooleanPropertyReplace() {
		//as array
		$result = $this->statement->format('PROD_%{0[refurbished]}_%{0[available]}', [['refurbished' => false, 'available' => true]], []);
		$this->assertEquals('PROD_0_1', $result);
	
		$result = $this->statement->format('PROD_#{refurbished:s}_#{available:s}', [['refurbished' => false, 'available' => true]], []);
		$this->assertEquals("PROD_''_'1'", $result);
	
		$result = $this->statement->format('PROD_#{refurbished:ss}_#{available:ss}', [['refurbished' => false, 'available' => true]], []);
		$this->assertEquals('PROD__1', $result);
	
		$result = $this->statement->format('PROD_#{refurbished:i}_#{available:i}', [['refurbished' => false, 'available' => true]], []);
		$this->assertEquals('PROD_0_1', $result);
	
		$result = $this->statement->format('PROD_#{refurbished:f}_#{available:f}', [['refurbished' => false, 'available' => true]], []);
		$this->assertEquals('PROD_0_1', $result);
	
		$result = $this->statement->format('PROD_#{refurbished:b}_#{available:b}', [['refurbished' => false, 'available' => true]], []);
		$this->assertEquals('PROD_0_1', $result);
	
		$result = $this->statement->format('PROD_#{refurbished:null}_#{available:null}', [['refurbished' => false, 'available' => true]], []);
		$this->assertEquals('PROD_NULL_NULL', $result);
	
		//as object
		$prod = new \stdClass();
		$prod->refurbished = false;
		$prod->available = true;
	
		$result = $this->statement->format('PROD_%{0[refurbished]}_%{0[available]}', [$prod], []);
		$this->assertEquals('PROD_0_1', $result);
	
		$result = $this->statement->format('PROD_#{refurbished:s}_#{available:s}', [$prod], []);
		$this->assertEquals("PROD_''_'1'", $result);
	
		$result = $this->statement->format('PROD_#{refurbished:ss}_#{available:ss}', [$prod], []);
		$this->assertEquals('PROD__1', $result);
	
		$result = $this->statement->format('PROD_#{refurbished:i}_#{available:i}', [$prod], []);
		$this->assertEquals('PROD_0_1', $result);
	
		$result = $this->statement->format('PROD_#{refurbished:f}_#{available:f}', [$prod], []);
		$this->assertEquals('PROD_0_1', $result);
	
		$result = $this->statement->format('PROD_#{refurbished:b}_#{available:b}', [$prod], []);
		$this->assertEquals('PROD_0_1', $result);
	
		$result = $this->statement->format('PROD_#{refurbished:null}_#{available:null}', [$prod], []);
		$this->assertEquals('PROD_NULL_NULL', $result);
	}
	
	/**
	 * Test parameters stored in an instance of ArrayObject
	 */
	public function testArrayObject() {
		$arr = new \ArrayObject();
		$arr['id'] = 4;
		$arr['code'] = 'ZYX987';
		$arr['price'] = '99.65';
		$arr['refurbished'] = true;
		$arr['available'] = 'f';
	
		$result = $this->statement->format('PROD_#{id}_#{code}_#{price:f}_%{0[refurbished]}_#{available:b}', [$arr], []);
		$this->assertEquals("PROD_4_'ZYX987'_99.65_1_0", $result);
	}
	
	/**
	 * Tests configuration options replacements
	 */
	public function testConfigReplace() {
		$result = $this->statement->format('@{entity.name}_@{entity.id}', [], ['entity.name' => 'users', 'entity.id' => 6]);
		$this->assertEquals('users_6', $result);
	
		$result = $this->statement->format('@{price}_@{refurbished}', [], ['price' => 29.75, 'refurbished' => true]);
		$this->assertEquals('29.75_1', $result);
	}
}
?>