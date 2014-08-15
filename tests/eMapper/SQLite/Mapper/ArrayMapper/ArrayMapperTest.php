<?php
namespace eMapper\SQLite\Mapper\ArrayMapper;

use eMapper\SQLite\SQLiteConfig;
use eMapper\Mapper\ArrayMapper\AbstractArrayMapperTest;
use eMapper\Result\ArrayType;

/**
 * Test SQLiteMapper mapping to array values
 * @author emaphp
 * @group sqlite
 * @group mapper
 */
class ArrayMapperTest extends AbstractArrayMapperTest {
	use SQLiteConfig;
	
	public function testRow() {
		//SQLITE3_BOTH
		$user = $this->mapper->type('array')->query("SELECT * FROM users WHERE user_id = 1");
		$this->assertInternalType('array', $user);
		$this->assertArrayHasKey(0, $user);
		$this->assertArrayHasKey(1, $user);
		$this->assertArrayHasKey(2, $user);
		$this->assertArrayHasKey(3, $user);
		$this->assertArrayHasKey(4, $user);
		$this->assertArrayHasKey(5, $user);
	
		$this->assertInternalType('integer', $user[0]);
		$this->assertEquals(1, $user[0]);
		$this->assertInternalType('string', $user[1]);
		$this->assertEquals('jdoe', $user[1]);
		$this->assertInternalType('string', $user[2]);
		$this->assertEquals('1987-08-10', $user[2]);
		$this->assertInternalType('string', $user[3]);
		$this->assertEquals('2013-08-10 19:57:15', $user[3]);
		$this->assertInternalType('string', $user[4]);
		$this->assertEquals('12:00:00', $user[4]);
		$this->assertInternalType('string', $user[5]);
		$this->assertEquals($this->getBlob(), $user[5]);
	
		$this->assertArrayHasKey('user_id', $user);
		$this->assertArrayHasKey('user_name', $user);
		$this->assertArrayHasKey('birth_date', $user);
		$this->assertArrayHasKey('last_login', $user);
		$this->assertArrayHasKey('newsletter_time', $user);
		$this->assertArrayHasKey('avatar', $user);
	
		$this->assertInternalType('integer', $user['user_id']);
		$this->assertEquals(1, $user['user_id']);
		$this->assertInternalType('string', $user['user_name']);
		$this->assertEquals('jdoe', $user['user_name']);
		$this->assertInternalType('string', $user['birth_date']);
		$this->assertEquals('1987-08-10', $user['birth_date']);
		$this->assertInternalType('string', $user['last_login']);
		$this->assertEquals('2013-08-10 19:57:15', $user['last_login']);
		$this->assertInternalType('string', $user['newsletter_time']);
		$this->assertEquals('12:00:00', $user['newsletter_time']);
		$this->assertInternalType('string', $user['avatar']);
		$this->assertEquals($this->getBlob(), $user['avatar']);
	
		//SQLITE3_ASSOC
		$user = $this->mapper->type('array', ArrayType::ASSOC)->query("SELECT * FROM users WHERE user_id = 1");
		$this->assertInternalType('array', $user);
		$this->assertArrayNotHasKey(0, $user);
		$this->assertArrayNotHasKey(1, $user);
		$this->assertArrayNotHasKey(2, $user);
		$this->assertArrayNotHasKey(3, $user);
		$this->assertArrayNotHasKey(4, $user);
		$this->assertArrayNotHasKey(5, $user);
	
		$this->assertArrayHasKey('user_id', $user);
		$this->assertArrayHasKey('user_name', $user);
		$this->assertArrayHasKey('birth_date', $user);
		$this->assertArrayHasKey('last_login', $user);
		$this->assertArrayHasKey('newsletter_time', $user);
		$this->assertArrayHasKey('avatar', $user);
	
		//SQLITE3_NUM
		$user = $this->mapper->type('array', ArrayType::NUM)->query("SELECT * FROM users WHERE user_id = 1");
		$this->assertInternalType('array', $user);
		$this->assertArrayHasKey(0, $user);
		$this->assertArrayHasKey(1, $user);
		$this->assertArrayHasKey(2, $user);
		$this->assertArrayHasKey(3, $user);
		$this->assertArrayHasKey(4, $user);
		$this->assertArrayHasKey(5, $user);
		$this->assertArrayNotHasKey('user_id', $user);
		$this->assertArrayNotHasKey('user_name', $user);
		$this->assertArrayNotHasKey('birth_date', $user);
		$this->assertArrayNotHasKey('last_login', $user);
		$this->assertArrayNotHasKey('newsletter_time', $user);
		$this->assertArrayNotHasKey('avatar', $user);
	}
	
	public function testList() {
		//SQLITE3_BOTH
		$users = $this->mapper->type('array[]')->query("SELECT * FROM users ORDER BY user_id ASC");
	
		$this->assertInternalType('array', $users);
		$this->assertCount(5, $users);
		$this->assertArrayHasKey(0, $users);
		$this->assertArrayHasKey(1, $users);
		$this->assertArrayHasKey(2, $users);
		$this->assertArrayHasKey(3, $users);
		$this->assertArrayHasKey(4, $users);
	
		$this->assertArrayHasKey('user_id', $users[0]);
		$this->assertInternalType('integer', $users[0]['user_id']);
		$this->assertEquals(1, $users[0]['user_id']);
		$this->assertArrayHasKey('user_name', $users[0]);
		$this->assertInternalType('string', $users[0]['user_name']);
		$this->assertEquals('jdoe', $users[0]['user_name']);
		$this->assertArrayHasKey('birth_date', $users[0]);
		$this->assertInternalType('string', $users[0]['birth_date']);
		$this->assertEquals('1987-08-10', $users[0]['birth_date']);
		$this->assertArrayHasKey('last_login', $users[0]);
		$this->assertInternalType('string', $users[0]['last_login']);
		$this->assertEquals('2013-08-10 19:57:15', $users[0]['last_login']);
		$this->assertArrayHasKey('newsletter_time', $users[0]);
		$this->assertInternalType('string', $users[0]['newsletter_time']);
		$this->assertEquals('12:00:00', $users[0]['newsletter_time']);
		$this->assertArrayHasKey('avatar', $users[0]);
		$this->assertInternalType('string', $users[0]['avatar']);
		$this->assertEquals($this->getBlob(), $users[0]['avatar']);
	
		$this->assertArrayHasKey(0, $users[0]);
		$this->assertInternalType('integer', $users[0][0]);
		$this->assertEquals(1, $users[0][0]);
		$this->assertArrayHasKey(1, $users[0]);
		$this->assertInternalType('string', $users[0][1]);
		$this->assertEquals('jdoe', $users[0][1]);
		$this->assertArrayHasKey(2, $users[0]);
		$this->assertInternalType('string', $users[0][2]);
		$this->assertEquals('1987-08-10', $users[0][2]);
		$this->assertArrayHasKey(3, $users[0]);
		$this->assertInternalType('string', $users[0][3]);
		$this->assertEquals('2013-08-10 19:57:15', $users[0][3]);
		$this->assertArrayHasKey(4, $users[0]);
		$this->assertInternalType('string', $users[0][4]);
		$this->assertEquals('12:00:00', $users[0][4]);
		$this->assertArrayHasKey(5, $users[0]);
		$this->assertInternalType('string', $users[0][5]);
		$this->assertEquals($this->getBlob(), $users[0][5]);
	
		//SQLITE3_NUM
		$users = $this->mapper->type('array[]', ArrayType::NUM)->query("SELECT * FROM users ORDER BY user_id ASC");
	
		$this->assertInternalType('array', $users);
		$this->assertCount(5, $users);
		$this->assertArrayHasKey(0, $users);
		$this->assertArrayHasKey(1, $users);
		$this->assertArrayHasKey(2, $users);
		$this->assertArrayHasKey(3, $users);
		$this->assertArrayHasKey(4, $users);
	
		$this->assertArrayNotHasKey('user_id', $users[0]);
		$this->assertArrayNotHasKey('user_name', $users[0]);
		$this->assertArrayNotHasKey('birth_date', $users[0]);
		$this->assertArrayNotHasKey('last_login', $users[0]);
		$this->assertArrayNotHasKey('newsletter_time', $users[0]);
		$this->assertArrayNotHasKey('avatar', $users[0]);
	
		$this->assertArrayHasKey(0, $users[0]);
		$this->assertArrayHasKey(1, $users[0]);
		$this->assertArrayHasKey(2, $users[0]);
		$this->assertArrayHasKey(3, $users[0]);
		$this->assertArrayHasKey(4, $users[0]);
		$this->assertArrayHasKey(5, $users[0]);
	
		//SQLITE3_ASSOC
		$users = $this->mapper->type('array[]', ArrayType::ASSOC)->query("SELECT * FROM users ORDER BY user_id ASC");
		$this->assertInternalType('array', $users);
		$this->assertCount(5, $users);
	
		$this->assertArrayHasKey(0, $users);
		$this->assertArrayHasKey(1, $users);
		$this->assertArrayHasKey(2, $users);
		$this->assertArrayHasKey(3, $users);
		$this->assertArrayHasKey(4, $users);
	
		$this->assertArrayNotHasKey(0, $users[0]);
		$this->assertArrayNotHasKey(1, $users[0]);
		$this->assertArrayNotHasKey(2, $users[0]);
		$this->assertArrayNotHasKey(3, $users[0]);
		$this->assertArrayNotHasKey(4, $users[0]);
		$this->assertArrayNotHasKey(5, $users[0]);
	
		$this->assertArrayHasKey('user_id', $users[0]);
		$this->assertArrayHasKey('user_name', $users[0]);
		$this->assertArrayHasKey('birth_date', $users[0]);
		$this->assertArrayHasKey('last_login', $users[0]);
		$this->assertArrayHasKey('newsletter_time', $users[0]);
		$this->assertArrayHasKey('avatar', $users[0]);
	}
	
	public function testIndexedList() {
		//SQLITE3_BOTH
		$users = $this->mapper->type('array[user_id]')->query("SELECT * FROM users ORDER BY user_id ASC");
		$this->assertInternalType('array', $users);
		$this->assertCount(5, $users);
		$this->assertArrayHasKey(1, $users);
		$this->assertArrayHasKey(2, $users);
		$this->assertArrayHasKey(3, $users);
		$this->assertArrayHasKey(4, $users);
		$this->assertArrayHasKey(5, $users);
	
		$this->assertArrayHasKey('user_id', $users[1]);
		$this->assertInternalType('integer', $users[1]['user_id']);
		$this->assertEquals(1, $users[1]['user_id']);
		$this->assertArrayHasKey('user_name', $users[1]);
		$this->assertInternalType('string', $users[1]['user_name']);
		$this->assertEquals('jdoe', $users[1]['user_name']);
		$this->assertArrayHasKey('birth_date', $users[1]);
		$this->assertInternalType('string', $users[1]['birth_date']);
		$this->assertEquals('1987-08-10', $users[1]['birth_date']);
		$this->assertArrayHasKey('last_login', $users[1]);
		$this->assertInternalType('string', $users[1]['last_login']);
		$this->assertEquals('2013-08-10 19:57:15', $users[1]['last_login']);
		$this->assertArrayHasKey('newsletter_time', $users[1]);
		$this->assertInternalType('string', $users[1]['newsletter_time']);
		$this->assertEquals('12:00:00', $users[1]['newsletter_time']);
		$this->assertArrayHasKey('avatar', $users[1]);
		$this->assertInternalType('string', $users[1]['avatar']);
		$this->assertEquals($this->getBlob(), $users[1]['avatar']);
	
		$this->assertArrayHasKey(0, $users[1]);
		$this->assertInternalType('integer', $users[1][0]);
		$this->assertEquals(1, $users[1][0]);
		$this->assertArrayHasKey(1, $users[1]);
		$this->assertInternalType('string', $users[1][1]);
		$this->assertEquals('jdoe', $users[1][1]);
		$this->assertArrayHasKey(2, $users[1]);
		$this->assertInternalType('string', $users[1][2]);
		$this->assertEquals('1987-08-10', $users[1][2]);
		$this->assertArrayHasKey(3, $users[1]);
		$this->assertInternalType('string', $users[1][3]);
		$this->assertEquals('2013-08-10 19:57:15', $users[1][3]);
		$this->assertArrayHasKey(4, $users[1]);
		$this->assertInternalType('string', $users[1][4]);
		$this->assertEquals('12:00:00', $users[1][4]);
		$this->assertArrayHasKey(5, $users[1]);
		$this->assertInternalType('string', $users[1][5]);
		$this->assertEquals($this->getBlob(), $users[1][5]);
	
		//SQLITE3_ASSOC
		$users = $this->mapper->type('array[user_id]', ArrayType::ASSOC)->query("SELECT * FROM users ORDER BY user_id ASC");
		$this->assertInternalType('array', $users);
	
		$this->assertArrayHasKey(1, $users);
		$this->assertArrayHasKey(2, $users);
		$this->assertArrayHasKey(3, $users);
		$this->assertArrayHasKey(4, $users);
		$this->assertArrayHasKey(5, $users);
	
		$this->assertArrayHasKey('user_id', $users[1]);
		$this->assertArrayHasKey('user_name', $users[1]);
		$this->assertArrayHasKey('birth_date', $users[1]);
		$this->assertArrayHasKey('last_login', $users[1]);
		$this->assertArrayHasKey('newsletter_time', $users[1]);
		$this->assertArrayHasKey('avatar', $users[1]);
	
		$this->assertArrayNotHasKey(0, $users[1]);
		$this->assertArrayNotHasKey(1, $users[1]);
		$this->assertArrayNotHasKey(2, $users[1]);
		$this->assertArrayNotHasKey(3, $users[1]);
		$this->assertArrayNotHasKey(4, $users[1]);
		$this->assertArrayNotHasKey(5, $users[1]);
	
		//SQLITE3_NUM
		$users = $this->mapper->type('array[0]', ArrayType::NUM)->query("SELECT * FROM users ORDER BY user_id ASC");
		$this->assertInternalType('array', $users);
	
		$this->assertArrayHasKey(1, $users);
		$this->assertArrayHasKey(2, $users);
		$this->assertArrayHasKey(3, $users);
		$this->assertArrayHasKey(4, $users);
		$this->assertArrayHasKey(5, $users);
	
		$this->assertArrayNotHasKey('user_id', $users[1]);
		$this->assertArrayNotHasKey('user_name', $users[1]);
		$this->assertArrayNotHasKey('birth_date', $users[1]);
		$this->assertArrayNotHasKey('last_login', $users[1]);
		$this->assertArrayNotHasKey('newsletter_time', $users[1]);
		$this->assertArrayNotHasKey('avatar', $users[1]);
	
		$this->assertArrayHasKey(0, $users[1]);
		$this->assertArrayHasKey(1, $users[1]);
		$this->assertArrayHasKey(2, $users[1]);
		$this->assertArrayHasKey(3, $users[1]);
		$this->assertArrayHasKey(4, $users[1]);
		$this->assertArrayHasKey(5, $users[1]);
	}
	
	public function testCustomIndexList() {
		//SQLITE3_BOTH
		$users = $this->mapper->type('array[user_id:string]')->query("SELECT * FROM users ORDER BY user_id ASC");
	
		$this->assertInternalType('array', $users);
		$this->assertCount(5, $users);
		$this->assertArrayHasKey('1', $users);
		$this->assertArrayHasKey('2', $users);
		$this->assertArrayHasKey('3', $users);
		$this->assertArrayHasKey('4', $users);
		$this->assertArrayHasKey('5', $users);
	
		$this->assertArrayHasKey('user_id', $users['1']);
		$this->assertInternalType('integer', $users['1']['user_id']);
		$this->assertEquals(1, $users['1']['user_id']);
		$this->assertArrayHasKey('user_name', $users['1']);
		$this->assertInternalType('string', $users['1']['user_name']);
		$this->assertEquals('jdoe', $users['1']['user_name']);
		$this->assertArrayHasKey('birth_date', $users['1']);
		$this->assertInternalType('string', $users['1']['birth_date']);
		$this->assertEquals('1987-08-10', $users['1']['birth_date']);
		$this->assertArrayHasKey('last_login', $users['1']);
		$this->assertInternalType('string', $users['1']['last_login']);
		$this->assertEquals('2013-08-10 19:57:15', $users['1']['last_login']);
		$this->assertArrayHasKey('newsletter_time', $users['1']);
		$this->assertInternalType('string', $users['1']['newsletter_time']);
		$this->assertEquals('12:00:00', $users['1']['newsletter_time']);
		$this->assertArrayHasKey('avatar', $users['1']);
		$this->assertInternalType('string', $users['1']['avatar']);
		$this->assertEquals($this->getBlob(), $users['1']['avatar']);
	
		$this->assertArrayHasKey(0, $users['1']);
		$this->assertInternalType('integer', $users['1'][0]);
		$this->assertEquals(1, $users['1'][0]);
		$this->assertArrayHasKey(1, $users['1']);
		$this->assertInternalType('string', $users['1'][1]);
		$this->assertEquals('jdoe', $users['1'][1]);
		$this->assertArrayHasKey(2, $users['1']);
		$this->assertInternalType('string', $users['1'][2]);
		$this->assertEquals('1987-08-10', $users['1'][2]);
		$this->assertArrayHasKey(3, $users['1']);
		$this->assertInternalType('string', $users['1'][3]);
		$this->assertEquals('2013-08-10 19:57:15', $users['1'][3]);
		$this->assertArrayHasKey(4, $users['1']);
		$this->assertInternalType('string', $users['1'][4]);
		$this->assertEquals('12:00:00', $users['1'][4]);
		$this->assertArrayHasKey(5, $users['1']);
		$this->assertInternalType('string', $users['1'][5]);
		$this->assertEquals($this->getBlob(), $users['1'][5]);
	
		//SQLITE3_ASSOC
		$users = $this->mapper->type('array[user_id:string]', ArrayType::ASSOC)->query("SELECT * FROM users ORDER BY user_id ASC");
		$this->assertInternalType('array', $users);
	
		$this->assertArrayHasKey('1', $users);
		$this->assertArrayHasKey('2', $users);
		$this->assertArrayHasKey('3', $users);
		$this->assertArrayHasKey('4', $users);
		$this->assertArrayHasKey('5', $users);
		$this->assertCount(5, $users);
	
		$this->assertArrayHasKey('user_id', $users['1']);
		$this->assertArrayHasKey('user_name', $users['1']);
		$this->assertArrayHasKey('birth_date', $users['1']);
		$this->assertArrayHasKey('last_login', $users['1']);
		$this->assertArrayHasKey('newsletter_time', $users['1']);
		$this->assertArrayHasKey('avatar', $users['1']);
		$this->assertArrayNotHasKey(0, $users['1']);
		$this->assertArrayNotHasKey(1, $users['1']);
		$this->assertArrayNotHasKey(2, $users['1']);
		$this->assertArrayNotHasKey(3, $users['1']);
		$this->assertArrayNotHasKey(4, $users['1']);
		$this->assertArrayNotHasKey(5, $users['1']);
	
		//SQLITE3_NUM
		$users = $this->mapper->type('array[0:string]', ArrayType::NUM)->query("SELECT * FROM users ORDER BY user_id ASC");
		$this->assertInternalType('array', $users);
	
		$this->assertArrayHasKey('1', $users);
		$this->assertArrayHasKey('2', $users);
		$this->assertArrayHasKey('3', $users);
		$this->assertArrayHasKey('4', $users);
		$this->assertArrayHasKey('5', $users);
		$this->assertCount(5, $users);
	
		$this->assertArrayNotHasKey('user_id', $users['1']);
		$this->assertArrayNotHasKey('user_name', $users['1']);
		$this->assertArrayNotHasKey('birth_date', $users['1']);
		$this->assertArrayNotHasKey('last_login', $users['1']);
		$this->assertArrayNotHasKey('newsletter_time', $users['1']);
		$this->assertArrayNotHasKey('avatar', $users['1']);
	
		$this->assertArrayHasKey(0, $users['1']);
		$this->assertArrayHasKey(1, $users['1']);
		$this->assertArrayHasKey(2, $users['1']);
		$this->assertArrayHasKey(3, $users['1']);
		$this->assertArrayHasKey(4, $users['1']);
		$this->assertArrayHasKey(5, $users['1']);
	}
}
?>