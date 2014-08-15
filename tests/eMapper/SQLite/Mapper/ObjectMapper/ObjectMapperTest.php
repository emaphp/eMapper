<?php
namespace eMapper\SQLite\Mapper\ObjectMapper;

use eMapper\SQLite\SQLiteConfig;
use eMapper\Mapper\ObjectMapper\AbstractObjectMapperTest;

/**
 * Tests SQLiteMapper mapping to objects
 * @author emaphp
 * @group sqlite
 * @group mapper
 */
class ObjectMapperTest extends AbstractObjectMapperTest {
	use SQLiteConfig;
	
	public function testRow() {
		$user = $this->mapper->type('obj:Acme\Generic\GenericUser')
		->query("SELECT * FROM users WHERE user_id = 1");
	
		$this->assertInstanceOf('Acme\Generic\GenericUser', $user);
	
		$this->assertInternalType('integer', $user->user_id);
		$this->assertEquals(1, $user->user_id);
	
		$this->assertInternalType('string', $user->user_name);
		$this->assertEquals('jdoe', $user->user_name);
	
		$this->assertInternalType('string', $user->birth_date);
		$this->assertEquals('1987-08-10', $user->birth_date);
	
		$this->assertInternalType('string', $user->newsletter_time);
		$this->assertEquals('12:00:00', $user->newsletter_time);
	
		$this->assertInternalType('string', $user->avatar);
		$this->assertEquals($this->getBlob(), $user->avatar);
	}
	
	public function testList() {
		$users = $this->mapper->type('obj:Acme\Generic\GenericUser[]')
		->query("SELECT * FROM users ORDER BY user_id ASC");
	
		$this->assertInternalType('array', $users);
		$this->assertCount(5, $users);
		$this->assertArrayHasKey(0, $users);
		$this->assertArrayHasKey(1, $users);
		$this->assertArrayHasKey(2, $users);
		$this->assertArrayHasKey(3, $users);
		$this->assertArrayHasKey(4, $users);
	
		$user = $users[0];
		$this->assertInstanceOf('Acme\Generic\GenericUser', $user);
	
		$this->assertInternalType('integer', $user->user_id);
		$this->assertEquals(1, $user->user_id);
	
		$this->assertInternalType('string', $user->user_name);
		$this->assertEquals('jdoe', $user->user_name);
	
		$this->assertInternalType('string', $user->birth_date);
		$this->assertEquals('1987-08-10', $user->birth_date);
	
		$this->assertInternalType('string', $user->newsletter_time);
		$this->assertEquals('12:00:00', $user->newsletter_time);
	
		$this->assertInternalType('string', $user->avatar);
		$this->assertEquals($this->getBlob(), $user->avatar);
	}
	
	public function testIndexedList() {
		$users = $this->mapper->type('obj:Acme\Generic\GenericUser[user_id]')
		->query("SELECT * FROM users ORDER BY user_id ASC");
	
		$this->assertInternalType('array', $users);
		$this->assertCount(5, $users);
	
		$this->assertArrayHasKey(1, $users);
		$this->assertArrayHasKey(2, $users);
		$this->assertArrayHasKey(3, $users);
		$this->assertArrayHasKey(4, $users);
		$this->assertArrayHasKey(5, $users);
	
		$user = $users[1];
		$this->assertInstanceOf('Acme\Generic\GenericUser', $user);
	
		$this->assertInternalType('integer', $user->user_id);
		$this->assertEquals(1, $user->user_id);
	
		$this->assertInternalType('string', $user->user_name);
		$this->assertEquals('jdoe', $user->user_name);
	
		$this->assertInternalType('string', $user->birth_date);
		$this->assertEquals('1987-08-10', $user->birth_date);
	
		$this->assertInternalType('string', $user->newsletter_time);
		$this->assertEquals('12:00:00', $user->newsletter_time);
	
		$this->assertInternalType('string', $user->avatar);
		$this->assertEquals($this->getBlob(), $user->avatar);
	}
	
	public function testCustomIndexList() {
		$users = $this->mapper->type('obj:Acme\Generic\GenericUser[user_id:string]')
		->query("SELECT * FROM users ORDER BY user_id ASC");
	
		$this->assertInternalType('array', $users);
		$this->assertCount(5, $users);
	
		$this->assertArrayHasKey('1', $users);
		$this->assertArrayHasKey('2', $users);
		$this->assertArrayHasKey('3', $users);
		$this->assertArrayHasKey('4', $users);
		$this->assertArrayHasKey('5', $users);
	
		$user = $users['1'];
		$this->assertInstanceOf('Acme\Generic\GenericUser', $user);
	
		$this->assertInternalType('integer', $user->user_id);
		$this->assertEquals(1, $user->user_id);
	
		$this->assertInternalType('string', $user->user_name);
		$this->assertEquals('jdoe', $user->user_name);
	
		$this->assertInternalType('string', $user->birth_date);
		$this->assertEquals('1987-08-10', $user->birth_date);
	
		$this->assertInternalType('string', $user->newsletter_time);
		$this->assertEquals('12:00:00', $user->newsletter_time);
	
		$this->assertInternalType('string', $user->avatar);
		$this->assertEquals($this->getBlob(), $user->avatar);
	}
}
?>