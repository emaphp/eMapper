<?php
namespace eMapper\SQLite;

use eMapper\SQLite\SQLiteConfig;
use eMapper\MapperTest;
use Acme\Storage\User;

/**
 * 
 * @author emaphp
 * @group storage
 */
class StorageTest extends MapperTest {
	use SQLiteConfig;
	
	protected $usersManager;
	
	public function __construct() {
		$this->usersManager = $this->getMapper()->newManager('Acme\Storage\User');
	}
	
	protected function getFilename() {
		return __DIR__ . '/storage.db';
	}
	
	protected function truncate() {
		$this->usersManager->truncate();
	}
	
	public function testSave() {
		$this->truncate();
		$login = new \Datetime;
		
		//entity
		$user = new User();
		$user->name = 'emaphp';
		$user->lastLogin = $login;
		$user->email = 'emaphp@github.com';
		
		$id = $this->usersManager->save($user);
		$this->assertInternalType('integer', $id);
		$this->assertEquals($id, $user->id);
		
		//stdclass
		$user = new \stdClass();
		$user->name = 'jdoe';
		$user->lastLogin = $login;
		$user->email = 'jdoe@github.com';
		
		$id = $this->usersManager->save($user);
		$this->assertInternalType('integer', $id);
		$this->assertEquals($id, $user->id);
		
		//array
		$user = [
			'name' => 'jarc',
			'lastLogin' => $login,
			'email' => 'jarc@github.com'
		];
		
		$id = $this->usersManager->save($user);
		$this->assertInternalType('integer', $id);
		$this->assertEquals($id, $user['id']);
	}
	
	public function testDuplicate() {
		$this->truncate();
		$login = new \Datetime;
		
		$mapper = $this->getMapper();
		$mapper->beginTransaction();
		$usersManager = $mapper->newManager('Acme\Storage\User');
		
		$user = new User();
		$user->name = 'emaphp';
		$user->lastLogin = $login;
		$user->email = 'emaphp@github.com';
		
		$id = $usersManager->save($user);
		$this->assertInternalType('integer', $id);
		$this->assertEquals($id, $user->id);
		
		$mapper->commit();
		
		$mapper->beginTransaction();
		$usersManager = $mapper->newManager('Acme\Storage\User');
		
		$user = new User();
		$user->name = 'emaphp';
		$user->lastLogin = $login;
		$user->email = 'emaphp@twitter.com';
		
		$newid = $usersManager->save($user);
		$this->assertInternalType('integer', $newid);
		$this->assertEquals($newid, $user->id);
		$this->assertEquals($newid, $id);
		
		//
		$newuser = $usersManager->findByPk($newid);
		$this->assertEquals($newuser->email, 'emaphp@twitter.com');
		
		$mapper->commit();
		$mapper->close();
		
		//TODO: for some reason database does not update the row
	}
}