<?php
namespace eMapper;

class AbstractStatementTest extends \PHPUnit_Framework_TestCase {
	protected $statement;
	
	public function setUp() {
		$this->statement = $this->getStatement();
	}
	
	public function testEmpty() {
		$result = $this->statement->build('', [], []);
		$this->assertEquals('', $result);
	}
	
	/**
	 * Tests integer type handler for mixed values
	 */
	public function testCacheKeyInt() {
		$result = $this->statement->build('USER_%{integer}', [25], []);
		$this->assertEquals('USER_25', $result);
	
		$result = $this->statement->build('USER_%{int}', [20], []);
		$this->assertEquals('USER_20', $result);
	
		$result = $this->statement->build('USER_%{i}', [14], []);
		$this->assertEquals('USER_14', $result);
	
		$result = $this->statement->build('USER_%{i}', ['6'], []);
		$this->assertEquals('USER_6', $result);
	
		$result = $this->statement->build('USER_%{i}', [3.65], []);
		$this->assertEquals('USER_3', $result);
	
		$result = $this->statement->build('USER_%{i}', [true], []);
		$this->assertEquals('USER_1', $result);
	
		$result = $this->statement->build('USER_%{i}', [false], []);
		$this->assertEquals('USER_0', $result);
	}
	
	/**
	 * Tests integer type handler for array values
	 */
	public function testIntArray() {
		$result = $this->statement->build('USER_%{integer}', [[1,2,3]], []);
		$this->assertEquals('USER_1,2,3', $result);
	
		$result = $this->statement->build('USER_%{integer}', [[1, "2", 3.65, true, false]], []);
		$this->assertEquals('USER_1,2,3,1,0', $result);
	}
	
	/**
	 * Tests float type handler for mixed values
	 */
	public function testFloat() {
		$result = $this->statement->build('PRICE_%{float}', [25], []);
		$this->assertEquals('PRICE_25', $result);
	
		$result = $this->statement->build('PRICE_%{double}', [2.75], []);
		$this->assertEquals('PRICE_2.75', $result);
	
		$result = $this->statement->build('PRICE_%{real}', ['6.75'], []);
		$this->assertEquals('PRICE_6.75', $result);
	
		$result = $this->statement->build('PRICE_%{f}', [true], []);
		$this->assertEquals('PRICE_1', $result);
	
		$result = $this->statement->build('PRICE_%{f}', [false], []);
		$this->assertEquals('PRICE_0', $result);
	}
	
	/**
	 * Tests float type handler for array values
	 */
	public function testFloatArray() {
		$result = $this->statement->build('PRICE_%{float}', [[1.56, 2.21, 3.45]], []);
		$this->assertEquals('PRICE_1.56,2.21,3.45', $result);
	
		$result = $this->statement->build('USER_%{f}', [[1, "2.45", true, false]], []);
		$this->assertEquals('USER_1,2.45,1,0', $result);
	}
	
	/**
	 * Tests array type handler for mixed values
	 */
	public function testString() {
		$result = $this->statement->build('PROD_%{string}', ['XYZ'], []);
		$this->assertEquals("PROD_'XYZ'", $result);
	
		$result = $this->statement->build('PROD_%{str}', ['ABC'], []);
		$this->assertEquals("PROD_'ABC'", $result);
	
		$result = $this->statement->build('PROD_%{s}', ['QWE'], []);
		$this->assertEquals("PROD_'QWE'", $result);
	
		$result = $this->statement->build('PROD_%{s}', [6], []);
		$this->assertEquals("PROD_'6'", $result);
	
		$result = $this->statement->build('PROD_%{s}', [3.65], []);
		$this->assertEquals("PROD_'3.65'", $result);
	
		$result = $this->statement->build('PROD_%{s}', [true], []);
		$this->assertEquals("PROD_'1'", $result);
	
		$result = $this->statement->build('PROD_%{s}', [false], []);
		$this->assertEquals("PROD_''", $result);
	}
	
	/**
	 * Tests string type handler for array values
	 */
	public function testStringArray() {
		$result = $this->statement->build('PROD_(%{string})', [['XYZ', 'ABC', 'QWE']], []);
		$this->assertEquals("PROD_('XYZ','ABC','QWE')", $result);
	
		$result = $this->statement->build('PROD_(%{s})', [[1, "QWE", 3.65, false, true]], []);
		$this->assertEquals("PROD_('1','QWE','3.65','','1')", $result);
	}
	
	/**
	 * Tests boolean type handler for mixed values
	 */
	public function testBoolean() {
		$result = $this->statement->build('CONN_%{boolean}', ['1'], []);
		$this->assertEquals('CONN_TRUE', $result);
	
		$result = $this->statement->build('CONN_%{bool}', ['0'], []);
		$this->assertEquals('CONN_FALSE', $result);
	
		$result = $this->statement->build('CONN_%{boolean}', ['T'], []);
		$this->assertEquals('CONN_TRUE', $result);
	
		$result = $this->statement->build('CONN_%{bool}', ['F'], []);
		$this->assertEquals('CONN_FALSE', $result);
	
		$result = $this->statement->build('CONN_%{boolean}', ['t'], []);
		$this->assertEquals('CONN_TRUE', $result);
	
		$result = $this->statement->build('CONN_%{bool}', ['f'], []);
		$this->assertEquals('CONN_FALSE', $result);
	
		$result = $this->statement->build('CONN_%{b}', [''], []);
		$this->assertEquals('CONN_FALSE', $result);
	
		$result = $this->statement->build('CONN_%{b}', [6], []);
		$this->assertEquals('CONN_TRUE', $result);
	
		$result = $this->statement->build('CONN_%{b}', [0], []);
		$this->assertEquals('CONN_FALSE', $result);
	
		$result = $this->statement->build('CONN_%{b}', [3.65], []);
		$this->assertEquals('CONN_TRUE', $result);
	
		$result = $this->statement->build('CONN_%{b}', [0.0], []);
		$this->assertEquals('CONN_FALSE', $result);
	
		$result = $this->statement->build('CONN_%{b}', [true], []);
		$this->assertEquals('CONN_TRUE', $result);
	
		$result = $this->statement->build('CONN_%{b}', [false], []);
		$this->assertEquals('CONN_FALSE', $result);
	}
	
	/**
	 * Tests boolean type handler for array values
	 */
	public function testBooleanArray() {
		$result = $this->statement->build('CONN_%{b}', [[false, true, false]], []);
		$this->assertEquals('CONN_FALSE,TRUE,FALSE', $result);
	
		$result = $this->statement->build('CONN_%{boolean}', [['1', '0', '', 'F', 't', 'f', 'T']], []);
		$this->assertEquals('CONN_TRUE,FALSE,FALSE,FALSE,TRUE,FALSE,TRUE', $result);
	
		$result = $this->statement->build('CONN_%{boolean}', [[10, 1, 0, 0.0, 3.65]], []);
		$this->assertEquals('CONN_TRUE,TRUE,FALSE,FALSE,TRUE', $result);
	}
	
	/**
	 * Tests unquoted string type handler for mixed values
	 */
	public function testSafeString() {
		$result = $this->statement->build('PROD_%{sstring}', ['XYZ'], []);
		$this->assertEquals('PROD_XYZ', $result);
	
		$result = $this->statement->build('PROD_%{sstr}', ['ABC'], []);
		$this->assertEquals('PROD_ABC', $result);
	
		$result = $this->statement->build('PROD_%{ss}', ['QWE'], []);
		$this->assertEquals('PROD_QWE', $result);
	
		$result = $this->statement->build('PROD_%{ss}', [6], []);
		$this->assertEquals('PROD_6', $result);
	
		$result = $this->statement->build('PROD_%{ss}', [3.65], []);
		$this->assertEquals('PROD_3.65', $result);
	
		$result = $this->statement->build('PROD_%{ss}', [true], []);
		$this->assertEquals('PROD_1', $result);
	
		$result = $this->statement->build('PROD_%{ss}', [false], []);
		$this->assertEquals('PROD_', $result);
	}
	
	/**
	 * Tests unquoted string type handler for array values
	 */
	public function testSafeStringArray() {
		$result = $this->statement->build('PROD:%{sstring}', [['XYZ', 'ABC', 'QWE']], []);
		$this->assertEquals('PROD:XYZ,ABC,QWE', $result);
	
		$result = $this->statement->build('PROD:%{ss}', [[1, "QWE", 3.65, false, true]], []);
		$this->assertEquals('PROD:1,QWE,3.65,,1', $result);
	}
	
	/**
	 * Tests null type handler for mixed values
	 */
	public function testNull() {
		$result = $this->statement->build('ENT_%{null}', [25], []);
		$this->assertEquals('ENT_NULL', $result);
	
		$result = $this->statement->build('ENT_%{null}', [2.75], []);
		$this->assertEquals('ENT_NULL', $result);
	
		$result = $this->statement->build('ENT_%{null}', ['XYZ'], []);
		$this->assertEquals('ENT_NULL', $result);
	
		$result = $this->statement->build('ENT_%{null}', [true], []);
		$this->assertEquals('ENT_NULL', $result);
	
		$result = $this->statement->build('ENT_%{null}', [false], []);
		$this->assertEquals('ENT_NULL', $result);
	}
	
	/**
	 * Tests null type handler for array values
	 */
	public function testNullArray() {
		$result = $this->statement->build('ENT_%{null}', [[1, 3.45, "XYZ", true, false]], []);
		$this->assertEquals('ENT_NULL,NULL,NULL,NULL,NULL', $result);
	}
	
	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testFailedPropertyReplace1() {
		$result = $this->statement->build('PROD_#{code}', [['xcode' => 'XYZ123']], []);
	}
	
	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testFailedPropertyReplace2() {
		$result = $this->statement->build('PROD_#{code}', [NULL], []);
	}
	
	/**
	 * Tests property replacements for string values
	 */
	public function testStringPropertyReplace() {
		//as array
		$result = $this->statement->build('PROD_%{0[code]}', [['code' => 'XYZ123']], []);
		$this->assertEquals("PROD_'XYZ123'", $result);
	
		$result = $this->statement->build('PROD_#{code:s}', [['code' => 'XYZ123']], []);
		$this->assertEquals("PROD_'XYZ123'", $result);
	
		$result = $this->statement->build('PROD_#{code:ss}', [['code' => 'XYZ123']], []);
		$this->assertEquals('PROD_XYZ123', $result);
	
		$result = $this->statement->build('PROD_#{code:i}', [['code' => 'XYZ123']], []);
		$this->assertEquals('PROD_0', $result);
	
		$result = $this->statement->build('PROD_#{code:f}', [['code' => 'XYZ123']], []);
		$this->assertEquals('PROD_0', $result);
	
		$result = $this->statement->build('PROD_#{code:b}', [['code' => 'XYZ123']], []);
		$this->assertEquals('PROD_TRUE', $result);
	
		$result = $this->statement->build('PROD_#{code:null}', [['code' => 'XYZ123']], []);
		$this->assertEquals('PROD_NULL', $result);
	
		//as object
		$prod = new \stdClass();
		$prod->code = 'XYZ123';
	
		$result = $this->statement->build('PROD_%{0[code]}', [$prod], []);
		$this->assertEquals("PROD_'XYZ123'", $result);
	
		$result = $this->statement->build('PROD_#{code:s}', [$prod], []);
		$this->assertEquals("PROD_'XYZ123'", $result);
	
		$result = $this->statement->build('PROD_#{code:ss}', [$prod], []);
		$this->assertEquals('PROD_XYZ123', $result);
	
		$result = $this->statement->build('PROD_#{code:i}', [$prod], []);
		$this->assertEquals('PROD_0', $result);
	
		$result = $this->statement->build('PROD_#{code:f}', [$prod], []);
		$this->assertEquals('PROD_0', $result);
	
		$result = $this->statement->build('PROD_#{code:b}', [$prod], []);
		$this->assertEquals('PROD_TRUE', $result);
	
		$result = $this->statement->build('PROD_#{code:null}', [$prod], []);
		$this->assertEquals('PROD_NULL', $result);
	}
	
	/**
	 * Tests property replacements for integer values
	 */
	public function testIntegerPropertyReplace() {
		//as array
		$result = $this->statement->build('PROD_%{0[id]}', [['id' => 42]], []);
		$this->assertEquals('PROD_42', $result);
	
		$result = $this->statement->build('PROD_#{id:s}', [['id' => 42]], []);
		$this->assertEquals("PROD_'42'", $result);
	
		$result = $this->statement->build('PROD_#{id:ss}', [['id' => 42]], []);
		$this->assertEquals('PROD_42', $result);
	
		$result = $this->statement->build('PROD_#{id:i}', [['id' => 42]], []);
		$this->assertEquals('PROD_42', $result);
	
		$result = $this->statement->build('PROD_#{id:f}', [['id' => 42]], []);
		$this->assertEquals('PROD_42', $result);
	
		$result = $this->statement->build('PROD_#{id:b}', [['id' => 42]], []);
		$this->assertEquals('PROD_TRUE', $result);
	
		$result = $this->statement->build('PROD_#{id:null}', [['id' => 42]], []);
		$this->assertEquals('PROD_NULL', $result);
	
		//as object
		$prod = new \stdClass();
		$prod->id = 42;
	
		$result = $this->statement->build('PROD_%{0[id]}', [$prod], []);
		$this->assertEquals('PROD_42', $result);
	
		$result = $this->statement->build('PROD_#{id:s}', [$prod], []);
		$this->assertEquals("PROD_'42'", $result);
	
		$result = $this->statement->build('PROD_#{id:ss}', [$prod], []);
		$this->assertEquals('PROD_42', $result);
	
		$result = $this->statement->build('PROD_#{id:i}', [$prod], []);
		$this->assertEquals('PROD_42', $result);
	
		$result = $this->statement->build('PROD_#{id:f}', [$prod], []);
		$this->assertEquals('PROD_42', $result);
	
		$result = $this->statement->build('PROD_#{id:b}', [$prod], []);
		$this->assertEquals('PROD_TRUE', $result);
	
		$result = $this->statement->build('PROD_#{id:null}', [$prod], []);
		$this->assertEquals('PROD_NULL', $result);
	}
	
	/**
	 * Tests property replacements for double values
	 */
	public function testFloatPropertyReplace() {
		//as array
		$result = $this->statement->build('PROD_%{0[price]}', [['price' => 39.95]], []);
		$this->assertEquals('PROD_39.95', $result);
	
		$result = $this->statement->build('PROD_#{price:s}', [['price' => 39.95]], []);
		$this->assertEquals("PROD_'39.95'", $result);
	
		$result = $this->statement->build('PROD_#{price:ss}', [['price' => 39.95]], []);
		$this->assertEquals('PROD_39.95', $result);
	
		$result = $this->statement->build('PROD_#{price:i}', [['price' => 39.95]], []);
		$this->assertEquals('PROD_39', $result);
	
		$result = $this->statement->build('PROD_#{price:f}', [['price' => 39.95]], []);
		$this->assertEquals('PROD_39.95', $result);
	
		$result = $this->statement->build('PROD_#{price:b}', [['price' => 39.95]], []);
		$this->assertEquals('PROD_TRUE', $result);
	
		$result = $this->statement->build('PROD_#{price:null}', [['price' => 39.95]], []);
		$this->assertEquals('PROD_NULL', $result);
	
		//as object
		$prod = new \stdClass();
		$prod->price = 39.95;
	
		$result = $this->statement->build('PROD_%{0[price]}', [$prod], []);
		$this->assertEquals('PROD_39.95', $result);
	
		$result = $this->statement->build('PROD_#{price:s}', [$prod], []);
		$this->assertEquals("PROD_'39.95'", $result);
	
		$result = $this->statement->build('PROD_#{price:ss}', [$prod], []);
		$this->assertEquals('PROD_39.95', $result);
	
		$result = $this->statement->build('PROD_#{price:i}', [$prod], []);
		$this->assertEquals('PROD_39', $result);
	
		$result = $this->statement->build('PROD_#{price:f}', [$prod], []);
		$this->assertEquals('PROD_39.95', $result);
	
		$result = $this->statement->build('PROD_#{price:b}', [$prod], []);
		$this->assertEquals('PROD_TRUE', $result);
	
		$result = $this->statement->build('PROD_#{price:null}', [$prod], []);
		$this->assertEquals('PROD_NULL', $result);
	}
	
	/**
	 * Tests property replacements for boolean values
	 */
	public function testBooleanPropertyReplace() {
		//as array
		$result = $this->statement->build('PROD_%{0[refurbished]}_%{0[available]}', [['refurbished' => false, 'available' => true]], []);
		$this->assertEquals('PROD_FALSE_TRUE', $result);
	
		$result = $this->statement->build('PROD_#{refurbished:s}_#{available:s}', [['refurbished' => false, 'available' => true]], []);
		$this->assertEquals("PROD_''_'1'", $result);
	
		$result = $this->statement->build('PROD_#{refurbished:ss}_#{available:ss}', [['refurbished' => false, 'available' => true]], []);
		$this->assertEquals('PROD__1', $result);
	
		$result = $this->statement->build('PROD_#{refurbished:i}_#{available:i}', [['refurbished' => false, 'available' => true]], []);
		$this->assertEquals('PROD_0_1', $result);
	
		$result = $this->statement->build('PROD_#{refurbished:f}_#{available:f}', [['refurbished' => false, 'available' => true]], []);
		$this->assertEquals('PROD_0_1', $result);
	
		$result = $this->statement->build('PROD_#{refurbished:b}_#{available:b}', [['refurbished' => false, 'available' => true]], []);
		$this->assertEquals('PROD_FALSE_TRUE', $result);
	
		$result = $this->statement->build('PROD_#{refurbished:null}_#{available:null}', [['refurbished' => false, 'available' => true]], []);
		$this->assertEquals('PROD_NULL_NULL', $result);
	
		//as object
		$prod = new \stdClass();
		$prod->refurbished = false;
		$prod->available = true;
	
		$result = $this->statement->build('PROD_%{0[refurbished]}_%{0[available]}', [$prod], []);
		$this->assertEquals('PROD_FALSE_TRUE', $result);
	
		$result = $this->statement->build('PROD_#{refurbished:s}_#{available:s}', [$prod], []);
		$this->assertEquals("PROD_''_'1'", $result);
	
		$result = $this->statement->build('PROD_#{refurbished:ss}_#{available:ss}', [$prod], []);
		$this->assertEquals('PROD__1', $result);
	
		$result = $this->statement->build('PROD_#{refurbished:i}_#{available:i}', [$prod], []);
		$this->assertEquals('PROD_0_1', $result);
	
		$result = $this->statement->build('PROD_#{refurbished:f}_#{available:f}', [$prod], []);
		$this->assertEquals('PROD_0_1', $result);
	
		$result = $this->statement->build('PROD_#{refurbished:b}_#{available:b}', [$prod], []);
		$this->assertEquals('PROD_FALSE_TRUE', $result);
	
		$result = $this->statement->build('PROD_#{refurbished:null}_#{available:null}', [$prod], []);
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
	
		$result = $this->statement->build('PROD_#{id}_#{code}_#{price:f}_%{0[refurbished]}_#{available:b}', [$arr], []);
		$this->assertEquals("PROD_4_'ZYX987'_99.65_TRUE_FALSE", $result);
	}
	
	/**
	 * Tests configuration options replacements
	 */
	public function testConfigReplace() {
		$result = $this->statement->build('@{entity.name}_@{entity.id}', [], ['entity.name' => 'users', 'entity.id' => 6]);
		$this->assertEquals('users_6', $result);
	
		$result = $this->statement->build('@{price}_@{refurbished}', [], ['price' => 29.75, 'refurbished' => true]);
		$this->assertEquals('29.75_1', $result);
	}
}
?>