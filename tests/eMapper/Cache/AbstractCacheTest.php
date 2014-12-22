<?php
namespace eMapper\Cache;

use eMapper\MapperTest;

abstract class AbstractCacheTest extends MapperTest {
	protected $provider;
	
	protected abstract function getProvider();
	protected abstract function getPrefix();
	
	public function testSetInteger() {
		$this->provider->delete($this->getPrefix() . 'set_integer');
	
		$this->mapper->cache($this->getPrefix() . 'set_integer', 60)->type('integer')->query("SELECT 1 + 1");
		$this->assertTrue($this->provider->exists($this->getPrefix() . 'set_integer'));
		$value = $this->provider->fetch($this->getPrefix() . 'set_integer');
		$this->assertEquals(2, $value);
	}
	
	public function testSetFloat() {
		$this->provider->delete($this->getPrefix() . 'set_float');
	
		$this->mapper->cache($this->getPrefix() . 'set_float', 60)->type('float')->query("SELECT price FROM products WHERE product_id = 1");
		$this->assertTrue($this->provider->exists($this->getPrefix() . 'set_float'));
		$value = $this->provider->fetch($this->getPrefix() . 'set_float');
		$this->assertEquals(150.65, $value);
	}
	
	public function testSetString() {
		$this->provider->delete($this->getPrefix() . 'set_string');
	
		$this->mapper->cache($this->getPrefix() . 'set_string', 60)->type('string')->query("SELECT user_name FROM users WHERE user_id = 1");
		$this->assertTrue($this->provider->exists($this->getPrefix() . 'set_string'));
		$value = $this->provider->fetch($this->getPrefix() . 'set_string');
		$this->assertEquals("jdoe", $value);
	}
	
	public function testSetArray() {
		$this->provider->delete($this->getPrefix() . 'set_array');
	
		$this->mapper->cache($this->getPrefix() . 'set_array', 60)->type('array')->query("SELECT * FROM users WHERE user_id = 1");
		$this->assertTrue($this->provider->exists($this->getPrefix() . 'set_array'));
		$value = $this->provider->fetch($this->getPrefix() . 'set_array');
	
		$this->assertInternalType('array', $value);
	
		$this->assertArrayHasKey('user_id', $value);
		$this->assertEquals(1, $value['user_id']);
	
		$this->assertArrayHasKey('user_name', $value);
		$this->assertEquals('jdoe', $value['user_name']);
	
		$this->assertArrayHasKey('birth_date', $value);
		$this->assertInstanceOf('DateTime', $value['birth_date']);
	
		$this->assertArrayHasKey('last_login', $value);
		$this->assertInstanceOf('DateTime', $value['last_login']);
	
		$this->assertArrayHasKey('newsletter_time', $value);
		$this->assertInternalType('string', $value['newsletter_time']);
	
		$this->assertArrayHasKey('avatar', $value);
		$this->assertEquals($this->getBlob(), $value['avatar']);
		
		//cache metakey
		$this->assertArrayHasKey('__cache__', $value);
		$meta = $value['__cache__'];
		$this->assertEquals($meta->class, 'eMapper\Result\Mapper\ArrayMapper');
		$this->assertEquals($meta->method, 'mapResult');
		$this->assertNull($meta->groups);
		$this->assertNull($meta->resultMap);
	}
	
	public function testSetArrayList() {
		$this->provider->delete($this->getPrefix() . 'set_arraylist');
	
		$this->mapper->cache($this->getPrefix() . 'set_arraylist', 60)->type('array[user_id]')->query("SELECT * FROM users ORDER BY user_id ASC");
		$this->assertTrue($this->provider->exists($this->getPrefix() . 'set_arraylist'));
		$value = $this->provider->fetch($this->getPrefix() . 'set_arraylist');
		
		$this->assertInternalType('array', $value);
	
		$this->assertArrayHasKey(1, $value);
		$this->assertInternalType('array', $value[1]);
		$this->assertArrayHasKey('user_id', $value[1]);
		$this->assertEquals(1, $value[1]['user_id']);
	
		$this->assertArrayHasKey(2, $value);
		$this->assertInternalType('array', $value[2]);
		$this->assertArrayHasKey('user_id', $value[2]);
		$this->assertEquals(2, $value[2]['user_id']);
	
		$this->assertArrayHasKey(3, $value);
		$this->assertInternalType('array', $value[3]);
		$this->assertArrayHasKey('user_id', $value[3]);
		$this->assertEquals(3, $value[3]['user_id']);
	
		$this->assertArrayHasKey(4, $value);
		$this->assertInternalType('array', $value[4]);
		$this->assertArrayHasKey('user_id', $value[4]);
		$this->assertEquals(4, $value[4]['user_id']);
	
		$this->assertArrayHasKey(5, $value);
		$this->assertInternalType('array', $value[5]);
		$this->assertArrayHasKey('user_id', $value[5]);
		$this->assertEquals(5, $value[5]['user_id']);
		
		//cache metakey
		$this->assertArrayHasKey('__cache__', $value);
		$meta = $value['__cache__'];
		$this->assertEquals($meta->class, 'eMapper\Result\Mapper\ArrayMapper');
		$this->assertEquals($meta->method, 'mapList');
		$this->assertNull($meta->groups);
		$this->assertNull($meta->resultMap);
	}
	
	public function testSetObject() {
		$this->provider->delete($this->getPrefix() . 'set_object');
	
		$this->mapper->cache($this->getPrefix() . 'set_object', 60)->type('object')->query("SELECT * FROM users WHERE user_id = 1");
		$this->assertTrue($this->provider->exists($this->getPrefix() . 'set_object'));
		$value = $this->provider->fetch($this->getPrefix() . 'set_object');

		$this->assertInstanceOf('stdClass', $value);
	
		$this->assertObjectHasAttribute('user_id', $value);
		$this->assertEquals(1, $value->user_id);
	
		$this->assertObjectHasAttribute('user_name', $value);
		$this->assertEquals('jdoe', $value->user_name);
	
		$this->assertObjectHasAttribute('birth_date', $value);
		$this->assertInstanceOf('DateTime', $value->birth_date);
	
		$this->assertObjectHasAttribute('last_login', $value);
		$this->assertInstanceOf('DateTime', $value->last_login);
	
		$this->assertObjectHasAttribute('newsletter_time', $value);
		$this->assertInternalType('string', $value->newsletter_time);
	
		$this->assertObjectHasAttribute('avatar', $value);
		$this->assertEquals($this->getBlob(), $value->avatar);
		
		//cache metakey
		$this->assertObjectHasAttribute('__cache__', $value);
		$meta = $value->__cache__;
		$this->assertEquals($meta->class, 'eMapper\Result\Mapper\StdClassMapper');
		$this->assertEquals($meta->method, 'mapResult');
		$this->assertNull($meta->groups);
		$this->assertNull($meta->resultMap);
	}
	
	public function testSetObjectList() {
		$this->provider->delete($this->getPrefix() .'set_objectlist');
	
		$this->mapper->cache($this->getPrefix() .'set_objectlist', 60)->type('object[user_id]')->query("SELECT user_id, user_name FROM users ORDER BY user_id ASC");
		
		$this->assertTrue($this->provider->exists($this->getPrefix() .'set_objectlist'));
		$value = $this->provider->fetch($this->getPrefix() .'set_objectlist');
		
		$this->assertInternalType('array', $value);
	
		$this->assertArrayHasKey(1, $value);
		$this->assertInstanceOf('stdClass', $value[1]);
		$this->assertObjectHasAttribute('user_id', $value[1]);
		$this->assertEquals(1, $value[1]->user_id);
	
		$this->assertArrayHasKey(2, $value);
		$this->assertInstanceOf('stdClass', $value[2]);
		$this->assertObjectHasAttribute('user_id', $value[2]);
		$this->assertEquals(2, $value[2]->user_id);
	
		$this->assertArrayHasKey(3, $value);
		$this->assertInstanceOf('stdClass', $value[3]);
		$this->assertObjectHasAttribute('user_id', $value[3]);
		$this->assertEquals(3, $value[3]->user_id);
	
		$this->assertArrayHasKey(4, $value);
		$this->assertInstanceOf('stdClass', $value[4]);
		$this->assertObjectHasAttribute('user_id', $value[4]);
		$this->assertEquals(4, $value[4]->user_id);
	
		$this->assertArrayHasKey(5, $value);
		$this->assertInstanceOf('stdClass', $value[5]);
		$this->assertObjectHasAttribute('user_id', $value[5]);
		$this->assertEquals(5, $value[5]->user_id);
		
		//cache metakey
		$this->assertArrayHasKey('__cache__', $value);
		$meta = $value['__cache__'];
		$this->assertEquals($meta->class, 'eMapper\Result\Mapper\StdClassMapper');
		$this->assertEquals($meta->method, 'mapList');
		$this->assertNull($meta->groups);
		$this->assertNull($meta->resultMap);
	}
}
?>