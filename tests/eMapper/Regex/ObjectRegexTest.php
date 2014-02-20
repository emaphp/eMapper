<?php
namespace eMapper\Regex;

use eMapper\Engine\Generic\GenericMapper;

/**
 * Tests parsing an object mapping expression
 * 
 * @author emaphp
 * @group regex
 */
class ObjectRegexTest extends \PHPUnit_Framework_TestCase {
	const REGEX = GenericMapper::OBJECT_TYPE_REGEX;
	
	public function testSimpleObject1() {
		$expr = 'object';
		$result = preg_match(self::REGEX, $expr, $matches);
		$this->assertEquals(1, $result);
		$this->assertCount(1, $matches);
		$this->assertEquals('object', $matches[0]);
	}
	
	public function testSimpleObject2() {
		$expr = 'obj';
		$result = preg_match(self::REGEX, $expr, $matches);
		$this->assertEquals(1, $result);
		$this->assertCount(1, $matches);
		$this->assertEquals('obj', $matches[0]);
	}
	
	public function testCustomObject1() {
		$expr = 'object:User';
		$result = preg_match(self::REGEX, $expr, $matches);
		$this->assertEquals(1, $result);
		$this->assertCount(2, $matches);
		$this->assertEquals('User', $matches[1]);
	}
	
	public function testCustomObject2() {
		$expr = 'obj:User';
		$result = preg_match(self::REGEX, $expr, $matches);
		$this->assertEquals(1, $result);
		$this->assertCount(2, $matches);
		$this->assertEquals('User', $matches[1]);
	}
	
	public function testCustomObject3() {
		$expr = 'object:Acme\Entity\User';
		$result = preg_match(self::REGEX, $expr, $matches);
		$this->assertEquals(1, $result);
		$this->assertCount(2, $matches);
		$this->assertEquals('Acme\Entity\User', $matches[1]);
	}
	
	public function testCustomObject4() {
		$expr = 'obj:Acme\Entity\User';
		$result = preg_match(self::REGEX, $expr, $matches);
		$this->assertEquals(1, $result);
		$this->assertCount(2, $matches);
		$this->assertEquals('Acme\Entity\User', $matches[1]);
	}
	
	public function testObjectList1() {
		$expr = 'object[]';
		$result = preg_match(self::REGEX, $expr, $matches);
		$this->assertEquals(1, $result);
		$this->assertCount(5, $matches);
		$this->assertEquals('[]', $matches[4]);
	}
	
	public function testObjectList2() {
		$expr = 'obj[]';
		$result = preg_match(self::REGEX, $expr, $matches);
		$this->assertEquals(1, $result);
		$this->assertCount(5, $matches);
		$this->assertEquals('[]', $matches[4]);
	}
	
	public function testObjectGroupedList1() {
		$expr = 'object<category>';
		$result = preg_match(self::REGEX, $expr, $matches);
		$this->assertEquals(1, $result);
		$this->assertCount(3, $matches);
		$this->assertEquals('category', $matches[2]);
	}
	
	public function testObjectGroupedList2() {
		$expr = 'obj<category>';
		$result = preg_match(self::REGEX, $expr, $matches);
		$this->assertEquals(1, $result);
		$this->assertCount(3, $matches);
		$this->assertEquals('category', $matches[2]);
	}
	
	public function testObjectGroupedList3() {
		$expr = 'object<category:int>';
		$result = preg_match(self::REGEX, $expr, $matches);
		$this->assertEquals(1, $result);
		$this->assertCount(4, $matches);
		$this->assertEquals('category', $matches[2]);
		$this->assertEquals('int', $matches[3]);
	}
	
	public function testObjectGroupedList4() {
		$expr = 'obj<category:int>';
		$result = preg_match(self::REGEX, $expr, $matches);
		$this->assertEquals(1, $result);
		$this->assertCount(4, $matches);
		$this->assertEquals('category', $matches[2]);
		$this->assertEquals('int', $matches[3]);
	}
	
	public function testObjectndexedList1() {
		$expr = 'object[product_id]';
		$result = preg_match(self::REGEX, $expr, $matches);
		$this->assertEquals(1, $result);
		$this->assertCount(6, $matches);
		$this->assertEquals('[product_id]', $matches[4]);
		$this->assertEquals('product_id', $matches[5]);
	}
	
	public function testObjectIndexedList2() {
		$expr = 'obj[product_id]';
		$result = preg_match(self::REGEX, $expr, $matches);
		$this->assertEquals(1, $result);
		$this->assertCount(6, $matches);
		$this->assertEquals('[product_id]', $matches[4]);
		$this->assertEquals('product_id', $matches[5]);
	}
	
	public function testObjectIndexedList3() {
		$expr = 'object[product_id:int]';
		$result = preg_match(self::REGEX, $expr, $matches);
		$this->assertEquals(1, $result);
		$this->assertCount(7, $matches);
		$this->assertEquals('[product_id:int]', $matches[4]);
		$this->assertEquals('product_id', $matches[5]);
		$this->assertEquals('int', $matches[6]);
	}
	
	public function testObjectIndexedList4() {
		$expr = 'obj[product_id:int]';
		$result = preg_match(self::REGEX, $expr, $matches);
		$this->assertEquals(1, $result);
		$this->assertCount(7, $matches);
		$this->assertEquals('[product_id:int]', $matches[4]);
		$this->assertEquals('product_id', $matches[5]);
		$this->assertEquals('int', $matches[6]);
	}
	
	public function testObjectGroupedIndexedList1() {
		$expr = 'object<category>[product_id]';
		$result = preg_match(self::REGEX, $expr, $matches);
		$this->assertEquals(1, $result);
		$this->assertCount(6, $matches);
		$this->assertEquals('category', $matches[2]);
		$this->assertEquals('[product_id]', $matches[4]);
		$this->assertEquals('product_id', $matches[5]);
	}
	
	public function testObjectGroupedIndexedList2() {
		$expr = 'obj<category:string>[product_id]';
		$result = preg_match(self::REGEX, $expr, $matches);
		$this->assertEquals(1, $result);
		$this->assertCount(6, $matches);
		$this->assertEquals('category', $matches[2]);
		$this->assertEquals('string', $matches[3]);
		$this->assertEquals('[product_id]', $matches[4]);
		$this->assertEquals('product_id', $matches[5]);
	}
	
	public function testObjectGroupedIndexedList3() {
		$expr = 'object<category>[product_id:int]';
		$result = preg_match(self::REGEX, $expr, $matches);
		$this->assertEquals(1, $result);
		$this->assertCount(7, $matches);
		$this->assertEquals('category', $matches[2]);
		$this->assertEquals('[product_id:int]', $matches[4]);
		$this->assertEquals('product_id', $matches[5]);
		$this->assertEquals('int', $matches[6]);
	}
	
	public function testObjectGroupedIndexedList4() {
		$expr = 'obj<category:string>[product_id:int]';
		$result = preg_match(self::REGEX, $expr, $matches);
		$this->assertEquals(1, $result);
		$this->assertCount(7, $matches);
		$this->assertEquals('category', $matches[2]);
		$this->assertEquals('string', $matches[3]);
		$this->assertEquals('[product_id:int]', $matches[4]);
		$this->assertEquals('product_id', $matches[5]);
		$this->assertEquals('int', $matches[6]);
	}
}
?>