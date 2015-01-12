<?php
namespace eMapper\SQLite\Storage;

use eMapper\SQLite\StorageTest;
use Acme\Storage\User;

/**
 * @group sqlite
 * @group storage
 */
class CascadeTest extends StorageTest {
	public function testSave() {
		$this->truncateTable('users');
		$login = new \Datetime;
		$mapper = $this->getMapper();
		$manager = $mapper->newManager('Acme\Storage\User');
		
		//entity
		$user = new User();
		$user->name = 'emaphp';
		$user->lastLogin = $login;
		$user->email = 'emaphp@github.com';
		
		$id = $manager->save($user);
		$this->assertInternalType('integer', $id);
		$this->assertEquals($id, $user->id);
		
		//stdclass
		$user = new \stdClass();
		$user->name = 'jdoe';
		$user->lastLogin = $login;
		$user->email = 'jdoe@github.com';
		
		$id = $manager->save($user);
		$this->assertInternalType('integer', $id);
		$this->assertEquals($id, $user->id);
		
		//array
		$user = [
			'name' => 'jarc',
			'lastLogin' => $login,
			'email' => 'jarc@github.com'
		];
		
		$id = $manager->save($user);
		$this->assertInternalType('integer', $id);
		$this->assertEquals($id, $user['id']);
		
		$mapper->close();
	}
	
	public function testDuplicate() {
		$this->truncateTable('users');
		$login = new \Datetime;
	
		$mapper = $this->getMapper();
		$manager = $mapper->newManager('Acme\Storage\User');
		
		$user = new User();
		$user->name = 'emaphp';
		$user->lastLogin = $login;
		$user->email = 'emaphp@github.com';
		
		$id = $manager->save($user);
		$this->assertInternalType('integer', $id);
		$this->assertEquals($id, $user->id);
		
		$user = new User();
		$user->name = 'emaphp';
		$user->lastLogin = $login;
		$user->email = 'emaphp@twitter.com';
		
		$newid = $manager->save($user);
		$this->assertInternalType('integer', $newid);
		$this->assertEquals($newid, $user->id);
		$this->assertEquals($newid, $id);
		
		//
		$newuser = $manager->findByPk($newid);
		$this->assertEquals($newuser->email, 'emaphp@twitter.com');
		
		$mapper->close();
		//WARNING: THIS DOES NOT UPDATE THE DATABASE CORRECTLY
	}
}