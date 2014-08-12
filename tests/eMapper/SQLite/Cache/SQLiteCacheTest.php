<?php
namespace eMapper\SQLite\Cache;

use eMapper\SQLite\SQLiteConfig;
use eMapper\Cache\AbstractCacheTest;

abstract class SQLiteCacheTest extends AbstractCacheTest {
	use SQLiteConfig;
	
	public function testSetArray() {
		$this->provider->delete($this->getPrefix() . 'set_array');
	
		$this->mapper->cache($this->getPrefix() . 'set_array', 60)->type('array')->query("SELECT * FROM users WHERE user_id = 1");
		$this->assertTrue($this->provider->exists($this->getPrefix() . 'set_array'));
		$value = $this->provider->fetch($this->getPrefix() . 'set_array');
		$this->assertInstanceOf('eMapper\Cache\Value\CacheValue', $value);
		$value = $value->getValue();
	
		$this->assertInternalType('array', $value);
	
		$this->assertArrayHasKey('user_id', $value);
		$this->assertEquals('1', $value['user_id']);
	
		$this->assertArrayHasKey('user_name', $value);
		$this->assertEquals('jdoe', $value['user_name']);
	
		$this->assertArrayHasKey('birth_date', $value);
		$this->assertEquals('1987-08-10', $value['birth_date']);
	
		$this->assertArrayHasKey('last_login', $value);
		$this->assertEquals('2013-08-10 19:57:15', $value['last_login']);
	
		$this->assertArrayHasKey('newsletter_time', $value);
		$this->assertInternalType('string', $value['newsletter_time']);
	
		$this->assertArrayHasKey('avatar', $value);
		$this->assertEquals($this->getBlob(), $value['avatar']);
	}
	
	public function testSetArrayList() {
		$this->provider->delete($this->getPrefix() . 'set_arraylist');
	
		$this->mapper->cache($this->getPrefix() . 'set_arraylist', 60)->type('array[user_id:int]')->query("SELECT * FROM users ORDER BY user_id ASC");
		$this->assertTrue($this->provider->exists($this->getPrefix() . 'set_arraylist'));
		$value = $this->provider->fetch($this->getPrefix() . 'set_arraylist');
		$this->assertInstanceOf('eMapper\Cache\Value\CacheValue', $value);
		$value = $value->getValue();
	
		$this->assertInternalType('array', $value);
	
		$this->assertArrayHasKey(1, $value);
		$this->assertInternalType('array', $value[1]);
		$this->assertArrayHasKey('user_id', $value[1]);
		$this->assertEquals('1', $value[1]['user_id']);
	
		$this->assertArrayHasKey(2, $value);
		$this->assertInternalType('array', $value[2]);
		$this->assertArrayHasKey('user_id', $value[2]);
		$this->assertEquals('2', $value[2]['user_id']);
	
		$this->assertArrayHasKey(3, $value);
		$this->assertInternalType('array', $value[3]);
		$this->assertArrayHasKey('user_id', $value[3]);
		$this->assertEquals('3', $value[3]['user_id']);
	
		$this->assertArrayHasKey(4, $value);
		$this->assertInternalType('array', $value[4]);
		$this->assertArrayHasKey('user_id', $value[4]);
		$this->assertEquals('4', $value[4]['user_id']);
	
		$this->assertArrayHasKey(5, $value);
		$this->assertInternalType('array', $value[5]);
		$this->assertArrayHasKey('user_id', $value[5]);
		$this->assertEquals('5', $value[5]['user_id']);
	}
	
	public function testSetObject() {
		$this->provider->delete($this->getPrefix() . 'set_object');
	
		$this->mapper->cache($this->getPrefix() . 'set_object', 60)->type('object')->query("SELECT * FROM users WHERE user_id = 1");
		$this->assertTrue($this->provider->exists($this->getPrefix() . 'set_object'));
		$value = $this->provider->fetch($this->getPrefix() . 'set_object');
		$this->assertInstanceOf('eMapper\Cache\Value\CacheValue', $value);
		$value = $value->getValue();
	
		$this->assertInstanceOf('stdClass', $value);
	
		$this->assertObjectHasAttribute('user_id', $value);
		$this->assertEquals('1', $value->user_id);
	
		$this->assertObjectHasAttribute('user_name', $value);
		$this->assertEquals('jdoe', $value->user_name);
	
		$this->assertObjectHasAttribute('birth_date', $value);
		$this->assertEquals('1987-08-10', $value->birth_date);
	
		$this->assertObjectHasAttribute('last_login', $value);
		$this->assertEquals('2013-08-10 19:57:15', $value->last_login);
	
		$this->assertObjectHasAttribute('newsletter_time', $value);
		$this->assertInternalType('string', $value->newsletter_time);
	
		$this->assertObjectHasAttribute('avatar', $value);
		$this->assertEquals($this->getBlob(), $value->avatar);
	}
	
	public function testSetObjectList() {
		$this->provider->delete($this->getPrefix() . 'set_objectlist');
	
		$this->mapper->cache($this->getPrefix() . 'set_objectlist', 60)->type('object[user_id:int]')->query("SELECT * FROM users ORDER BY user_id ASC");
		$this->assertTrue($this->provider->exists($this->getPrefix() . 'set_objectlist'));
		$value = $this->provider->fetch($this->getPrefix() . 'set_objectlist');
		$this->assertInstanceOf('eMapper\Cache\Value\CacheValue', $value);
		$value = $value->getValue();
	
		$this->assertInternalType('array', $value);
	
		$this->assertArrayHasKey(1, $value);
		$this->assertInstanceOf('stdClass', $value[1]);
		$this->assertObjectHasAttribute('user_id', $value[1]);
		$this->assertEquals('1', $value[1]->user_id);
	
		$this->assertArrayHasKey(2, $value);
		$this->assertInstanceOf('stdClass', $value[2]);
		$this->assertObjectHasAttribute('user_id', $value[2]);
		$this->assertEquals('2', $value[2]->user_id);
	
		$this->assertArrayHasKey(3, $value);
		$this->assertInstanceOf('stdClass', $value[3]);
		$this->assertObjectHasAttribute('user_id', $value[3]);
		$this->assertEquals('3', $value[3]->user_id);
	
		$this->assertArrayHasKey(4, $value);
		$this->assertInstanceOf('stdClass', $value[4]);
		$this->assertObjectHasAttribute('user_id', $value[4]);
		$this->assertEquals('4', $value[4]->user_id);
	
		$this->assertArrayHasKey(5, $value);
		$this->assertInstanceOf('stdClass', $value[5]);
		$this->assertObjectHasAttribute('user_id', $value[5]);
		$this->assertEquals('5', $value[5]->user_id);
	}
}
?>