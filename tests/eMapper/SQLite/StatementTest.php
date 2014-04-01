<?php
namespace eMapper\SQLite;

use eMapper\Engine\SQLite\Statement\SQLiteStatement;
use eMapper\Engine\SQLite\Type\SQLiteTypeManager;

/**
 * Test SQLiteStatement class
 * @author emaphp
 * @group sqlite
 * @group statement
 */
class StatementTest extends SQLiteTest {
	public $statement;
	
	public function __construct() {
		self::setUpBeforeClass();
		$this->statement = new SQLiteStatement(self::$conn, new SQLiteTypeManager());
	}
	
	public function testEmpty() {
		$result = $this->statement->build('', array(), array());
		$this->assertEquals('', $result);
	}
	
	/**
	 * Tests integer type handler for mixed values
	 */
	public function testCacheKeyInt() {
		$result = $this->statement->build('USER_%{integer}', array(25), array());
		$this->assertEquals('USER_25', $result);
	
		$result = $this->statement->build('USER_%{int}', array(20), array());
		$this->assertEquals('USER_20', $result);
	
		$result = $this->statement->build('USER_%{i}', array(14), array());
		$this->assertEquals('USER_14', $result);
	
		$result = $this->statement->build('USER_%{i}', array('6'), array());
		$this->assertEquals('USER_6', $result);
	
		$result = $this->statement->build('USER_%{i}', array(3.65), array());
		$this->assertEquals('USER_3', $result);
	
		$result = $this->statement->build('USER_%{i}', array(true), array());
		$this->assertEquals('USER_1', $result);
	
		$result = $this->statement->build('USER_%{i}', array(false), array());
		$this->assertEquals('USER_0', $result);
	}
	
	/**
	 * Tests integer type handler for array values
	 */
	public function testIntArray() {
		$result = $this->statement->build('USER_%{integer}', array(array(1,2,3)), array());
		$this->assertEquals('USER_1,2,3', $result);
	
		$result = $this->statement->build('USER_%{integer}', array(array(1, "2", 3.65, true,false)), array());
		$this->assertEquals('USER_1,2,3,1,0', $result);
	}
	
	/**
	 * Tests float type handler for mixed values
	 */
	public function testFloat() {
		$result = $this->statement->build('PRICE_%{float}', array(25), array());
		$this->assertEquals('PRICE_25', $result);
	
		$result = $this->statement->build('PRICE_%{double}', array(2.75), array());
		$this->assertEquals('PRICE_2.75', $result);
	
		$result = $this->statement->build('PRICE_%{real}', array('6.75'), array());
		$this->assertEquals('PRICE_6.75', $result);
	
		$result = $this->statement->build('PRICE_%{f}', array(true), array());
		$this->assertEquals('PRICE_1', $result);
	
		$result = $this->statement->build('PRICE_%{f}', array(false), array());
		$this->assertEquals('PRICE_0', $result);
	}
	
	/**
	 * Tests float type handler for array values
	 */
	public function testFloatArray() {
		$result = $this->statement->build('PRICE_%{float}', array(array(1.56,2.21,3.45)), array());
		$this->assertEquals('PRICE_1.56,2.21,3.45', $result);
	
		$result = $this->statement->build('USER_%{f}', array(array(1, "2.45", true,false)), array());
		$this->assertEquals('USER_1,2.45,1,0', $result);
	}
	
	/**
	 * Tests array type handler for mixed values
	 */
	public function testString() {
		$result = $this->statement->build('PROD_%{string}', array('XYZ'), array());
		$this->assertEquals("PROD_'XYZ'", $result);
	
		$result = $this->statement->build('PROD_%{str}', array('ABC'), array());
		$this->assertEquals("PROD_'ABC'", $result);
	
		$result = $this->statement->build('PROD_%{s}', array('QWE'), array());
		$this->assertEquals("PROD_'QWE'", $result);
	
		$result = $this->statement->build('PROD_%{s}', array(6), array());
		$this->assertEquals("PROD_'6'", $result);
	
		$result = $this->statement->build('PROD_%{s}', array(3.65), array());
		$this->assertEquals("PROD_'3.65'", $result);
	
		$result = $this->statement->build('PROD_%{s}', array(true), array());
		$this->assertEquals("PROD_'1'", $result);
	
		$result = $this->statement->build('PROD_%{s}', array(false), array());
		$this->assertEquals("PROD_''", $result);
	}
	
	/**
	 * Tests string type handler for array values
	 */
	public function testStringArray() {
		$result = $this->statement->build('PROD_(%{string})', array(array('XYZ', 'ABC', 'QWE')), array());
		$this->assertEquals("PROD_('XYZ','ABC','QWE')", $result);
	
		$result = $this->statement->build('PROD_(%{s})', array(array(1, "QWE", 3.65, false, true)), array());
		$this->assertEquals("PROD_('1','QWE','3.65','','1')", $result);
	}
	
	/**
	 * Tests boolean type handler for mixed values
	 */
	public function testBoolean() {
		$result = $this->statement->build('CONN_%{boolean}', array('1'), array());
		$this->assertEquals('CONN_1', $result);
	
		$result = $this->statement->build('CONN_%{bool}', array('0'), array());
		$this->assertEquals('CONN_0', $result);
	
		$result = $this->statement->build('CONN_%{boolean}', array('T'), array());
		$this->assertEquals('CONN_1', $result);
	
		$result = $this->statement->build('CONN_%{bool}', array('F'), array());
		$this->assertEquals('CONN_0', $result);
	
		$result = $this->statement->build('CONN_%{boolean}', array('t'), array());
		$this->assertEquals('CONN_1', $result);
	
		$result = $this->statement->build('CONN_%{bool}', array('f'), array());
		$this->assertEquals('CONN_0', $result);
	
		$result = $this->statement->build('CONN_%{b}', array(''), array());
		$this->assertEquals('CONN_0', $result);
	
		$result = $this->statement->build('CONN_%{b}', array(6), array());
		$this->assertEquals('CONN_1', $result);
	
		$result = $this->statement->build('CONN_%{b}', array(0), array());
		$this->assertEquals('CONN_0', $result);
	
		$result = $this->statement->build('CONN_%{b}', array(3.65), array());
		$this->assertEquals('CONN_1', $result);
	
		$result = $this->statement->build('CONN_%{b}', array(0.0), array());
		$this->assertEquals('CONN_0', $result);
	
		$result = $this->statement->build('CONN_%{b}', array(true), array());
		$this->assertEquals('CONN_1', $result);
	
		$result = $this->statement->build('CONN_%{b}', array(false), array());
		$this->assertEquals('CONN_0', $result);
	}
	
	/**
	 * Tests boolean type handler for array values
	 */
	public function testBooleanArray() {
		$result = $this->statement->build('CONN_%{b}', array(array(false,true,false)), array());
		$this->assertEquals('CONN_0,1,0', $result);
	
		$result = $this->statement->build('CONN_%{boolean}', array(array('1', '0', '', 'F', 't', 'f', 'T')), array());
		$this->assertEquals('CONN_1,0,0,0,1,0,1', $result);
	
		$result = $this->statement->build('CONN_%{boolean}', array(array(10, 1, 0, 0.0, 3.65)), array());
		$this->assertEquals('CONN_1,1,0,0,1', $result);
	}
	
	/**
	 * Tests unquoted string type handler for mixed values
	 */
	public function testUnquotedString() {
		$result = $this->statement->build('PROD_%{ustring}', array('XYZ'), array());
		$this->assertEquals('PROD_XYZ', $result);
	
		$result = $this->statement->build('PROD_%{ustr}', array('ABC'), array());
		$this->assertEquals('PROD_ABC', $result);
	
		$result = $this->statement->build('PROD_%{us}', array('QWE'), array());
		$this->assertEquals('PROD_QWE', $result);
	
		$result = $this->statement->build('PROD_%{us}', array(6), array());
		$this->assertEquals('PROD_6', $result);
	
		$result = $this->statement->build('PROD_%{us}', array(3.65), array());
		$this->assertEquals('PROD_3.65', $result);
	
		$result = $this->statement->build('PROD_%{us}', array(true), array());
		$this->assertEquals('PROD_1', $result);
	
		$result = $this->statement->build('PROD_%{us}', array(false), array());
		$this->assertEquals('PROD_', $result);
	}
	
	/**
	 * Tests unquoted string type handler for array values
	 */
	public function testUnquotedStringArray() {
		$result = $this->statement->build('PROD:%{ustring}', array(array('XYZ', 'ABC', 'QWE')), array());
		$this->assertEquals('PROD:XYZ,ABC,QWE', $result);
	
		$result = $this->statement->build('PROD:%{us}', array(array(1, "QWE", 3.65, false, true)), array());
		$this->assertEquals('PROD:1,QWE,3.65,,1', $result);
	}
	
	/**
	 * Tests null type handler for mixed values
	 */
	public function testNull() {
		$result = $this->statement->build('ENT_%{null}', array(25), array());
		$this->assertEquals('ENT_NULL', $result);
	
		$result = $this->statement->build('ENT_%{null}', array(2.75), array());
		$this->assertEquals('ENT_NULL', $result);
	
		$result = $this->statement->build('ENT_%{null}', array('XYZ'), array());
		$this->assertEquals('ENT_NULL', $result);
	
		$result = $this->statement->build('ENT_%{null}', array(true), array());
		$this->assertEquals('ENT_NULL', $result);
	
		$result = $this->statement->build('ENT_%{null}', array(false), array());
		$this->assertEquals('ENT_NULL', $result);
	}
	
	/**
	 * Tests null type handler for array values
	 */
	public function testNullArray() {
		$result = $this->statement->build('ENT_%{null}', array(array(1, 3.45, "XYZ",true,false)), array());
		$this->assertEquals('ENT_NULL,NULL,NULL,NULL,NULL', $result);
	}
	
	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testFailedPropertyReplace1() {
		$result = $this->statement->build('PROD_#{code}', array(array('xcode' => 'XYZ123')), array());
	}
	
	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testFailedPropertyReplace2() {
		$result = $this->statement->build('PROD_#{code}', array(NULL), array());
	}
	
	/**
	 * Tests property replacements for string values
	 */
	public function testStringPropertyReplace() {
		//as array
		$result = $this->statement->build('PROD_%{0[code]}', array(array('code' => 'XYZ123')), array());
		$this->assertEquals("PROD_'XYZ123'", $result);
	
		$result = $this->statement->build('PROD_#{code:s}', array(array('code' => 'XYZ123')), array());
		$this->assertEquals("PROD_'XYZ123'", $result);
	
		$result = $this->statement->build('PROD_#{code:us}', array(array('code' => 'XYZ123')), array());
		$this->assertEquals('PROD_XYZ123', $result);
	
		$result = $this->statement->build('PROD_#{code:i}', array(array('code' => 'XYZ123')), array());
		$this->assertEquals('PROD_0', $result);
	
		$result = $this->statement->build('PROD_#{code:f}', array(array('code' => 'XYZ123')), array());
		$this->assertEquals('PROD_0', $result);
	
		$result = $this->statement->build('PROD_#{code:b}', array(array('code' => 'XYZ123')), array());
		$this->assertEquals('PROD_1', $result);
	
		$result = $this->statement->build('PROD_#{code:null}', array(array('code' => 'XYZ123')), array());
		$this->assertEquals('PROD_NULL', $result);
	
		//as object
		$prod = new \stdClass();
		$prod->code = 'XYZ123';
	
		$result = $this->statement->build('PROD_%{0[code]}', array($prod), array());
		$this->assertEquals("PROD_'XYZ123'", $result);
	
		$result = $this->statement->build('PROD_#{code:s}', array($prod), array());
		$this->assertEquals("PROD_'XYZ123'", $result);
	
		$result = $this->statement->build('PROD_#{code:us}', array($prod), array());
		$this->assertEquals('PROD_XYZ123', $result);
	
		$result = $this->statement->build('PROD_#{code:i}', array($prod), array());
		$this->assertEquals('PROD_0', $result);
	
		$result = $this->statement->build('PROD_#{code:f}', array($prod), array());
		$this->assertEquals('PROD_0', $result);
	
		$result = $this->statement->build('PROD_#{code:b}', array($prod), array());
		$this->assertEquals('PROD_1', $result);
	
		$result = $this->statement->build('PROD_#{code:null}', array($prod), array());
		$this->assertEquals('PROD_NULL', $result);
	}
	
	/**
	 * Tests property replacements for integer values
	 */
	public function testIntegerPropertyReplace() {
		//as array
		$result = $this->statement->build('PROD_%{0[id]}', array(array('id' => 42)), array());
		$this->assertEquals('PROD_42', $result);
	
		$result = $this->statement->build('PROD_#{id:s}', array(array('id' => 42)), array());
		$this->assertEquals("PROD_'42'", $result);
	
		$result = $this->statement->build('PROD_#{id:us}', array(array('id' => 42)), array());
		$this->assertEquals('PROD_42', $result);
	
		$result = $this->statement->build('PROD_#{id:i}', array(array('id' => 42)), array());
		$this->assertEquals('PROD_42', $result);
	
		$result = $this->statement->build('PROD_#{id:f}', array(array('id' => 42)), array());
		$this->assertEquals('PROD_42', $result);
	
		$result = $this->statement->build('PROD_#{id:b}', array(array('id' => 42)), array());
		$this->assertEquals('PROD_1', $result);
	
		$result = $this->statement->build('PROD_#{id:null}', array(array('id' => 42)), array());
		$this->assertEquals('PROD_NULL', $result);
	
		//as object
		$prod = new \stdClass();
		$prod->id = 42;
	
		$result = $this->statement->build('PROD_%{0[id]}', array($prod), array());
		$this->assertEquals('PROD_42', $result);
	
		$result = $this->statement->build('PROD_#{id:s}', array($prod), array());
		$this->assertEquals("PROD_'42'", $result);
	
		$result = $this->statement->build('PROD_#{id:us}', array($prod), array());
		$this->assertEquals('PROD_42', $result);
	
		$result = $this->statement->build('PROD_#{id:i}', array($prod), array());
		$this->assertEquals('PROD_42', $result);
	
		$result = $this->statement->build('PROD_#{id:f}', array($prod), array());
		$this->assertEquals('PROD_42', $result);
	
		$result = $this->statement->build('PROD_#{id:b}', array($prod), array());
		$this->assertEquals('PROD_1', $result);
	
		$result = $this->statement->build('PROD_#{id:null}', array($prod), array());
		$this->assertEquals('PROD_NULL', $result);
	}
	
	/**
	 * Tests property replacements for double values
	 */
	public function testFloatPropertyReplace() {
		//as array
		$result = $this->statement->build('PROD_%{0[price]}', array(array('price' => 39.95)), array());
		$this->assertEquals('PROD_39.95', $result);
	
		$result = $this->statement->build('PROD_#{price:s}', array(array('price' => 39.95)), array());
		$this->assertEquals("PROD_'39.95'", $result);
	
		$result = $this->statement->build('PROD_#{price:us}', array(array('price' => 39.95)), array());
		$this->assertEquals('PROD_39.95', $result);
	
		$result = $this->statement->build('PROD_#{price:i}', array(array('price' => 39.95)), array());
		$this->assertEquals('PROD_39', $result);
	
		$result = $this->statement->build('PROD_#{price:f}', array(array('price' => 39.95)), array());
		$this->assertEquals('PROD_39.95', $result);
	
		$result = $this->statement->build('PROD_#{price:b}', array(array('price' => 39.95)), array());
		$this->assertEquals('PROD_1', $result);
	
		$result = $this->statement->build('PROD_#{price:null}', array(array('price' => 39.95)), array());
		$this->assertEquals('PROD_NULL', $result);
	
		//as object
		$prod = new \stdClass();
		$prod->price = 39.95;
	
		$result = $this->statement->build('PROD_%{0[price]}', array($prod), array());
		$this->assertEquals('PROD_39.95', $result);
	
		$result = $this->statement->build('PROD_#{price:s}', array($prod), array());
		$this->assertEquals("PROD_'39.95'", $result);
	
		$result = $this->statement->build('PROD_#{price:us}', array($prod), array());
		$this->assertEquals('PROD_39.95', $result);
	
		$result = $this->statement->build('PROD_#{price:i}', array($prod), array());
		$this->assertEquals('PROD_39', $result);
	
		$result = $this->statement->build('PROD_#{price:f}', array($prod), array());
		$this->assertEquals('PROD_39.95', $result);
	
		$result = $this->statement->build('PROD_#{price:b}', array($prod), array());
		$this->assertEquals('PROD_1', $result);
	
		$result = $this->statement->build('PROD_#{price:null}', array($prod), array());
		$this->assertEquals('PROD_NULL', $result);
	}
	
	/**
	 * Tests property replacements for boolean values
	 */
	public function testBooleanPropertyReplace() {
		//as array
		$result = $this->statement->build('PROD_%{0[refurbished]}_%{0[available]}', array(array('refurbished' => false, 'available' => true)), array());
		$this->assertEquals('PROD_0_1', $result);
	
		$result = $this->statement->build('PROD_#{refurbished:s}_#{available:s}', array(array('refurbished' => false, 'available' => true)), array());
		$this->assertEquals("PROD_''_'1'", $result);
	
		$result = $this->statement->build('PROD_#{refurbished:us}_#{available:us}', array(array('refurbished' => false, 'available' => true)), array());
		$this->assertEquals('PROD__1', $result);
	
		$result = $this->statement->build('PROD_#{refurbished:i}_#{available:i}', array(array('refurbished' => false, 'available' => true)), array());
		$this->assertEquals('PROD_0_1', $result);
	
		$result = $this->statement->build('PROD_#{refurbished:f}_#{available:f}', array(array('refurbished' => false, 'available' => true)), array());
		$this->assertEquals('PROD_0_1', $result);
	
		$result = $this->statement->build('PROD_#{refurbished:b}_#{available:b}', array(array('refurbished' => false, 'available' => true)), array());
		$this->assertEquals('PROD_0_1', $result);
	
		$result = $this->statement->build('PROD_#{refurbished:null}_#{available:null}', array(array('refurbished' => false, 'available' => true)), array());
		$this->assertEquals('PROD_NULL_NULL', $result);
	
		//as object
		$prod = new \stdClass();
		$prod->refurbished = false;
		$prod->available = true;
	
		$result = $this->statement->build('PROD_%{0[refurbished]}_%{0[available]}', array($prod), array());
		$this->assertEquals('PROD_0_1', $result);
	
		$result = $this->statement->build('PROD_#{refurbished:s}_#{available:s}', array($prod), array());
		$this->assertEquals("PROD_''_'1'", $result);
	
		$result = $this->statement->build('PROD_#{refurbished:us}_#{available:us}', array($prod), array());
		$this->assertEquals('PROD__1', $result);
	
		$result = $this->statement->build('PROD_#{refurbished:i}_#{available:i}', array($prod), array());
		$this->assertEquals('PROD_0_1', $result);
	
		$result = $this->statement->build('PROD_#{refurbished:f}_#{available:f}', array($prod), array());
		$this->assertEquals('PROD_0_1', $result);
	
		$result = $this->statement->build('PROD_#{refurbished:b}_#{available:b}', array($prod), array());
		$this->assertEquals('PROD_0_1', $result);
	
		$result = $this->statement->build('PROD_#{refurbished:null}_#{available:null}', array($prod), array());
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
	
		$result = $this->statement->build('PROD_#{id}_#{code}_#{price:f}_%{0[refurbished]}_#{available:b}', array($arr), array());
		$this->assertEquals("PROD_4_'ZYX987'_99.65_1_0", $result);
	}
	
	/**
	 * Tests configuration options replacements
	 */
	public function testConfigReplace() {
		$result = $this->statement->build('@{entity.name}_@{entity.id}', array(), array('entity.name' => 'users', 'entity.id' => 6));
		$this->assertEquals('users_6', $result);
	
		$result = $this->statement->build('@{price}_@{refurbished}', array(), array('price' => 29.75, 'refurbished' => true));
		$this->assertEquals('29.75_1', $result);
	}
}
?>