<?php
namespace eMapper\Cache;

use Acme\Entity\Product;
use Acme\RGBColor;

/**
 * Test APCProvider with different values
 * @author emaphp
 * @group cache
 * @group apc
 */
class APCTest extends \PHPUnit_Framework_TestCase {
	/**
	 * APCProvider
	 * @var APCProvider
	 */
	public $provider;
	
	protected function setUp() {
		try {
			$this->provider = new APCProvider();
		}
		catch (\RuntimeException $re) {
			$this->markTestSkipped(
					'The APC extension is not available.'
			);
		}
	}
	
	public function testInteger() {
		$this->provider->delete('apc_integer');
	
		$this->provider->store('apc_integer', 100, 60);
		$value = $this->provider->fetch('apc_integer');
		$this->assertEquals(100, $value);
	}
	
	public function testFloat() {
		$this->provider->delete('apc_float');
		$this->provider->store('apc_float', 4.75, 60);
		$value = $this->provider->fetch('apc_float');
		$this->assertEquals(4.75, $value);
	}
	
	public function testString() {
		$this->provider->delete('apc_string');
		$this->provider->store('apc_string', "string value", 60);
		$value = $this->provider->fetch('apc_string');
		$this->assertEquals("string value", $value);
	}
	
	public function testArray() {
		$this->provider->delete('apc_array');
		
		$arr = ['int' => 100, 'float' => 4.75, 'string' => 'string value', 4 => 'four'];
		$this->provider->store('apc_array', $arr, 60);
		$value = $this->provider->fetch('apc_array');
		
		$this->assertInternalType('array', $value);
		$this->assertCount(4, $arr);
		
		$this->assertArrayHasKey('int', $value);
		$this->assertInternalType('integer', $value['int']);
		$this->assertEquals(100, $value['int']);
		
		$this->assertArrayHasKey('float', $value);
		$this->assertInternalType('float', $value['float']);
		$this->assertEquals(4.75, $value['float']);
		
		$this->assertArrayHasKey('string', $value);
		$this->assertInternalType('string', $value['string']);
		$this->assertEquals('string value', $value['string']);
		
		$this->assertArrayHasKey(4, $value);
		$this->assertInternalType('string', $value[4]);
		$this->assertEquals('four', $value[4]);
	}
	
	public function testStdClass() {
		$this->provider->delete('apc_stdclass');
		
		$obj = new \stdClass();
		$obj->int = 100;
		$obj->float = 4.75;
		$obj->string = 'string value';
		$obj->arr = ['abc', 123, 2.5];
		
		$this->provider->store('apc_stdclass', $obj);
		$value = $this->provider->fetch('apc_stdclass');
		
		$this->assertInstanceOf('stdClass', $value);
		
		$this->assertObjectHasAttribute('int', $value);
		$this->assertInternalType('integer', $value->int);
		$this->assertEquals(100, $value->int);
		
		$this->assertObjectHasAttribute('float', $value);
		$this->assertInternalType('float', $value->float);
		$this->assertEquals(4.75, $value->float);
		
		$this->assertObjectHasAttribute('string', $value);
		$this->assertInternalType('string', $value->string);
		$this->assertEquals('string value', $value->string);
		
		$this->assertObjectHasAttribute('arr', $value);
		$this->assertInternalType('array', $value->arr);
		
		$this->assertArrayHasKey(0, $value->arr);
		$this->assertInternalType('string', $value->arr[0]);
		$this->assertEquals('abc', $value->arr[0]);
		
		$this->assertArrayHasKey(1, $value->arr);
		$this->assertInternalType('integer', $value->arr[1]);
		$this->assertEquals(123, $value->arr[1]);
		
		$this->assertArrayHasKey(2, $value->arr);
		$this->assertInternalType('float', $value->arr[2]);
		$this->assertEquals(2.5, $value->arr[2]);
	}
	
	public function testClass() {
		$this->provider->delete('apc_object');
		
		$obj = new Product();
		$obj->id = 123;
		$obj->code = 'xxx789';
		$obj->setCategory('Random');
		$obj->color = new RGBColor(127, 127, 0);
		
		$this->provider->store('apc_object', $obj, 60);
		$value = $this->provider->fetch('apc_object');
		
		$this->assertInstanceOf('Acme\Entity\Product', $value);
		$this->assertEquals(123, $value->id);
		$this->assertEquals('xxx789', $value->code);
		$this->assertEquals('Random', $value->getCategory());
		
		$this->assertInstanceOf('Acme\RGBColor', $value->color);
		$this->assertEquals(127, $value->color->red);
		$this->assertEquals(127, $value->color->green);
		$this->assertEquals(0, $value->color->blue);
	}
}

?>