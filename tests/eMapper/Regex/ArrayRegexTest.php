<?php
namespace eMapper\Regex;

/**
 * 
 * @author emaphp
 * @group regex
 */
class ArrayRegexTest extends \PHPUnit_Framework_TestCase {
	const REGEX = '@^(?:array|arr+)(?:<(\w+)(?::([A-z]{1}[\w]*))?>)?(\[\]|\[(\w+)(?::([A-z]{1}[\w]*))?\])?$@';
	
	public function testSimpleArray1() {
		$expr = 'array';
		$result = preg_match(self::REGEX, $expr, $matches);
		$this->assertEquals(1, $result);
		$this->assertCount(1, $matches);
		$this->assertEquals('array', $matches[0]);
	}
	
	public function testSimpleArray2() {
		$expr = 'arr';
		$result = preg_match(self::REGEX, $expr, $matches);
		$this->assertEquals(1, $result);
		$this->assertCount(1, $matches);
		$this->assertEquals('arr', $matches[0]);
	}
	
	public function testArrayList1() {
		$expr = 'array[]';
		$result = preg_match(self::REGEX, $expr, $matches);
		$this->assertEquals(1, $result);
		$this->assertCount(4, $matches);
		$this->assertEquals('[]', $matches[3]);
	}
	
	public function testArrayList2() {
		$expr = 'arr[]';
		$result = preg_match(self::REGEX, $expr, $matches);
		$this->assertEquals(1, $result);
		$this->assertCount(4, $matches);
		$this->assertEquals('[]', $matches[3]);
	}
	
	public function testArrayGroupedList1() {
		$expr = 'array<category>';
		$result = preg_match(self::REGEX, $expr, $matches);
		$this->assertEquals(1, $result);
		$this->assertCount(2, $matches);
		$this->assertEquals('category', $matches[1]);
	}
	
	public function testArrayGroupedList2() {
		$expr = 'arr<category>';
		$result = preg_match(self::REGEX, $expr, $matches);
		$this->assertEquals(1, $result);
		$this->assertCount(2, $matches);
		$this->assertEquals('category', $matches[1]);
	}
	
	public function testArrayGroupedList3() {
		$expr = 'array<category:int>';
		$result = preg_match(self::REGEX, $expr, $matches);
		$this->assertEquals(1, $result);
		$this->assertCount(3, $matches);
		$this->assertEquals('category', $matches[1]);
		$this->assertEquals('int', $matches[2]);
	}
	
	public function testArrayGroupedList4() {
		$expr = 'arr<category:int>';
		$result = preg_match(self::REGEX, $expr, $matches);
		$this->assertEquals(1, $result);
		$this->assertCount(3, $matches);
		$this->assertEquals('category', $matches[1]);
		$this->assertEquals('int', $matches[2]);
	}
	
	public function testArrayIndexedList1() {
		$expr = 'array[product_id]';
		$result = preg_match(self::REGEX, $expr, $matches);
		$this->assertEquals(1, $result);
		$this->assertCount(5, $matches);
		$this->assertEquals('[product_id]', $matches[3]);
		$this->assertEquals('product_id', $matches[4]);
	}
	
	public function testArrayIndexedList2() {
		$expr = 'arr[product_id]';
		$result = preg_match(self::REGEX, $expr, $matches);
		$this->assertEquals(1, $result);
		$this->assertCount(5, $matches);
		$this->assertEquals('[product_id]', $matches[3]);
		$this->assertEquals('product_id', $matches[4]);
	}
	
	public function testArrayIndexedList3() {
		$expr = 'array[product_id:int]';
		$result = preg_match(self::REGEX, $expr, $matches);
		$this->assertEquals(1, $result);
		$this->assertCount(6, $matches);
		$this->assertEquals('[product_id:int]', $matches[3]);
		$this->assertEquals('product_id', $matches[4]);
		$this->assertEquals('int', $matches[5]);
	}
	
	public function testArrayIndexedList4() {
		$expr = 'arr[product_id:int]';
		$result = preg_match(self::REGEX, $expr, $matches);
		$this->assertEquals(1, $result);
		$this->assertCount(6, $matches);
		$this->assertEquals('[product_id:int]', $matches[3]);
		$this->assertEquals('product_id', $matches[4]);
		$this->assertEquals('int', $matches[5]);
	}
	
	public function testArrayGroupedIndexedList1() {
		$expr = 'array<category>[product_id]';
		$result = preg_match(self::REGEX, $expr, $matches);
		$this->assertEquals(1, $result);
		$this->assertCount(5, $matches);
		$this->assertEquals('category', $matches[1]);
		$this->assertEquals('[product_id]', $matches[3]);
		$this->assertEquals('product_id', $matches[4]);
	}
	
	public function testArrayGroupedIndexedList2() {
		$expr = 'array<category:string>[product_id]';
		$result = preg_match(self::REGEX, $expr, $matches);
		$this->assertEquals(1, $result);
		$this->assertCount(5, $matches);
		$this->assertEquals('category', $matches[1]);
		$this->assertEquals('string', $matches[2]);
		$this->assertEquals('[product_id]', $matches[3]);
		$this->assertEquals('product_id', $matches[4]);
	}
	
	public function testArrayGroupedIndexedList3() {
		$expr = 'array<category>[product_id:int]';
		$result = preg_match(self::REGEX, $expr, $matches);
		$this->assertEquals(1, $result);
		$this->assertCount(6, $matches);
		$this->assertEquals('category', $matches[1]);
		$this->assertEquals('[product_id:int]', $matches[3]);
		$this->assertEquals('product_id', $matches[4]);
		$this->assertEquals('int', $matches[5]);
	}
	
	public function testArrayGroupedIndexedList4() {
		$expr = 'array<category:string>[product_id:int]';
		$result = preg_match(self::REGEX, $expr, $matches);
		$this->assertEquals(1, $result);
		$this->assertCount(6, $matches);
		$this->assertEquals('category', $matches[1]);
		$this->assertEquals('string', $matches[2]);
		$this->assertEquals('[product_id:int]', $matches[3]);
		$this->assertEquals('product_id', $matches[4]);
		$this->assertEquals('int', $matches[5]);
	}
}
?>