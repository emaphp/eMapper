<?php
namespace eMapper\Cache\Memcache;

use Acme\Entity\Product;
use Acme\RGBColor;
use eMapper\Cache\MemcacheProvider;

/**
 * Test MemcacheProvider with different values
 * @author emaphp
 * @group cache
 * @group memcache
 */
class MemcacheTest extends \PHPUnit_Framework_TestCase {
	/**
	 * Memcache provider
	 * @var MemcacheProvider
	 */
	public $provider;
	
	protected function setUp() {
		try {
			$this->provider = new MemcacheProvider();
		}
		catch (\RuntimeException $re) {
			$this->markTestSkipped(
					'The Memcache extension is not available.'
			);
		}
	}
	
	public function testInteger() {
		$this->provider->delete('memcache_integer');
	
		$this->provider->store('memcache_integer', 100, 60);
		$value = $this->provider->fetch('memcache_integer');
		$this->assertEquals(100, $value);
	}
	
	public function testFloat() {
		$this->provider->delete('memcache_float');
		$this->provider->store('memcache_float', 4.75, 60);
		$value = $this->provider->fetch('memcache_float');
		$this->assertEquals(4.75, $value);
	}
	
	public function testString() {
		$this->provider->delete('memcache_string');
		$this->provider->store('memcache_string', "string value", 60);
		$value = $this->provider->fetch('memcache_string');
		$this->assertEquals("string value", $value);
	}
	
	public function testArray() {
		$this->provider->delete('memcache_array');
	
		$arr = ['int' => 100, 'float' => 4.75, 'string' => 'string value', 4 => 'four'];
		$this->provider->store('memcache_array', $arr, 60);
		$value = $this->provider->fetch('memcache_array');
	
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
		$this->provider->delete('memcache_stdclass');
	
		$obj = new \stdClass();
		$obj->int = 100;
		$obj->float = 4.75;
		$obj->string = 'string value';
		$obj->arr = ['abc', 123, 2.5];
	
		$this->provider->store('memcache_stdclass', $obj);
		$value = $this->provider->fetch('memcache_stdclass');
	
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
		$this->provider->delete('memcache_object');
	
		$obj = new Product();
		$obj->id = 123;
		$obj->code = 'xxx789';
		$obj->setCategory('Random');
		$obj->color = new RGBColor(127, 127, 0);
	
		$this->provider->store('memcache_object', $obj, 60);
		$value = $this->provider->fetch('memcache_object');
	
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