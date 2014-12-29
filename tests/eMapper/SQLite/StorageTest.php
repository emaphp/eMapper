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
		$user->birthDate = '1984-07-05';
		$user->lastLogin = $login;
		$user->notify = '15:00';
		
		$id = $this->usersManager->save($user);
		$this->assertInternalType('integer', $id);
		$this->assertEquals($id, $user->id);
		
		//stdclass
		$user = new \stdClass();
		$user->name = 'jdoe';
		$user->birthDate = '1987-11-03';
		$user->lastLogin = $login;
		$user->notify = '16:00';
		
		$id = $this->usersManager->save($user);
		$this->assertInternalType('integer', $id);
		$this->assertEquals($id, $user->id);
		
		//array
		$user = [
			'name' => 'jarc',
			'birthDate' => '1965-05-30',
			'lastLogin' => $login,
			'notify' => '17:00'
		];
		
		$id = $this->usersManager->save($user);
		$this->assertInternalType('integer', $id);
		$this->assertEquals($id, $user['id']);
	}
}