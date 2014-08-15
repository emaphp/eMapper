<?php
namespace eMapper\SQLite\Mapper\ObjectMapper;

use eMapper\SQLite\SQLiteConfig;
use eMapper\Mapper\ObjectMapper\AbstractDefaultMapTest;

/**
 * Tests SQLiteMapper obtaining default stdClass instances
 * @author emaphp
 * @group sqlite
 * @group mapper
 */
class DefaultMapTest extends AbstractDefaultMapTest {
	use SQLiteConfig;
	
	public function testRow() {
		$user = $this->mapper->type('object')->query("SELECT * FROM users WHERE user_id = 1");
	
		$this->assertInstanceOf('stdClass', $user);
	
		$this->assertObjectHasAttribute('user_id', $user);
		$this->assertInternalType('integer', $user->user_id);
		$this->assertEquals(1, $user->user_id);
	
		$this->assertObjectHasAttribute('user_name', $user);
		$this->assertInternalType('string', $user->user_name);
		$this->assertEquals('jdoe', $user->user_name);
	
		$this->assertObjectHasAttribute('birth_date', $user);
		$this->assertInternalType('string', $user->birth_date);
		$this->assertEquals('1987-08-10', $user->birth_date);
	
		$this->assertObjectHasAttribute('last_login', $user);
		$this->assertInternalType('string', $user->last_login);
		$this->assertEquals('2013-08-10 19:57:15', $user->last_login);
	
		$this->assertObjectHasAttribute('newsletter_time', $user);
		$this->assertInternalType('string', $user->newsletter_time);
		$this->assertEquals('12:00:00', $user->newsletter_time);
	
		$this->assertObjectHasAttribute('avatar', $user);
		$this->assertInternalType('string', $user->avatar);
		$this->assertEquals($this->getBlob(), $user->avatar);
	}
	
	public function testList() {
		$users = $this->mapper->type('object[]')->query("SELECT * FROM users ORDER BY user_id ASC");
	
		$this->assertInternalType('array', $users);
		$this->assertCount(5, $users);
		$this->assertArrayHasKey(0, $users);
		$this->assertArrayHasKey(1, $users);
		$this->assertArrayHasKey(2, $users);
		$this->assertArrayHasKey(3, $users);
		$this->assertArrayHasKey(4, $users);
	
		$user = $users[0];
		$this->assertInstanceOf('stdClass', $user);
	
		$this->assertObjectHasAttribute('user_id', $user);
		$this->assertInternalType('integer', $user->user_id);
		$this->assertEquals(1, $user->user_id);
	
		$this->assertObjectHasAttribute('user_name', $user);
		$this->assertInternalType('string', $user->user_name);
		$this->assertEquals('jdoe', $user->user_name);
	
		$this->assertObjectHasAttribute('birth_date', $user);
		$this->assertInternalType('string', $user->birth_date);
		$this->assertEquals('1987-08-10', $user->birth_date);
	
		$this->assertObjectHasAttribute('last_login', $user);
		$this->assertInternalType('string', $user->last_login);
		$this->assertEquals('2013-08-10 19:57:15', $user->last_login);
	
		$this->assertObjectHasAttribute('newsletter_time', $user);
		$this->assertInternalType('string', $user->newsletter_time);
		$this->assertEquals('12:00:00', $user->newsletter_time);
	
		$this->assertObjectHasAttribute('avatar', $user);
		$this->assertInternalType('string', $user->avatar);
		$this->assertEquals($this->getBlob(), $user->avatar);
	}
	
	public function testIndexedList() {
		$users = $this->mapper->type('object[user_id]')->query("SELECT * FROM users ORDER BY user_id ASC");
	
		$this->assertInternalType('array', $users);
		$this->assertCount(5, $users);
		$this->assertArrayHasKey(1, $users);
		$this->assertArrayHasKey(2, $users);
		$this->assertArrayHasKey(3, $users);
		$this->assertArrayHasKey(4, $users);
		$this->assertArrayHasKey(5, $users);
	
		$user = $users[1];
		$this->assertInstanceOf('stdClass', $user);
	
		$this->assertObjectHasAttribute('user_id', $user);
		$this->assertInternalType('integer', $user->user_id);
		$this->assertEquals(1, $user->user_id);
	
		$this->assertObjectHasAttribute('user_name', $user);
		$this->assertInternalType('string', $user->user_name);
		$this->assertEquals('jdoe', $user->user_name);
	
		$this->assertObjectHasAttribute('birth_date', $user);
		$this->assertInternalType('string', $user->birth_date);
		$this->assertEquals('1987-08-10', $user->birth_date);
	
		$this->assertObjectHasAttribute('last_login', $user);
		$this->assertInternalType('string', $user->last_login);
		$this->assertEquals('2013-08-10 19:57:15', $user->last_login);
	
		$this->assertObjectHasAttribute('newsletter_time', $user);
		$this->assertInternalType('string', $user->newsletter_time);
		$this->assertEquals('12:00:00', $user->newsletter_time);
	
		$this->assertObjectHasAttribute('avatar', $user);
		$this->assertInternalType('string', $user->avatar);
		$this->assertEquals($this->getBlob(), $user->avatar);
	}
	
	public function testCustomIndexList() {
		$users = $this->mapper->type('object[user_id:string]')->query("SELECT * FROM users ORDER BY user_id ASC");
	
		$this->assertInternalType('array', $users);
		$this->assertCount(5, $users);
		$this->assertArrayHasKey('1', $users);
		$this->assertArrayHasKey('2', $users);
		$this->assertArrayHasKey('3', $users);
		$this->assertArrayHasKey('4', $users);
		$this->assertArrayHasKey('5', $users);
	
		$user = $users['1'];
		$this->assertInstanceOf('stdClass', $user);
	
		$this->assertObjectHasAttribute('user_id', $user);
		$this->assertInternalType('integer', $user->user_id);
		$this->assertEquals(1, $user->user_id);
	
		$this->assertObjectHasAttribute('user_name', $user);
		$this->assertInternalType('string', $user->user_name);
		$this->assertEquals('jdoe', $user->user_name);
	
		$this->assertObjectHasAttribute('birth_date', $user);
		$this->assertInternalType('string', $user->birth_date);
		$this->assertEquals('1987-08-10', $user->birth_date);
	
		$this->assertObjectHasAttribute('last_login', $user);
		$this->assertInternalType('string', $user->last_login);
		$this->assertEquals('2013-08-10 19:57:15', $user->last_login);
	
		$this->assertObjectHasAttribute('newsletter_time', $user);
		$this->assertInternalType('string', $user->newsletter_time);
		$this->assertEquals('12:00:00', $user->newsletter_time);
	
		$this->assertObjectHasAttribute('avatar', $user);
		$this->assertInternalType('string', $user->avatar);
		$this->assertEquals($this->getBlob(), $user->avatar);
	}
}
?>