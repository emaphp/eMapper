<?php
namespace eMapper\SQLite;

use eMapper\SQLite\SQLiteConfig;
use eMapper\MapperTest;
use Acme\Storage\User;
use Acme\Storage\Profile;
use eMapper\Query\Attr;

/**
 * 
 * @author emaphp
 * @group storage
 */
class StorageTest extends MapperTest {
	use SQLiteConfig;
	
	protected $usersManager;
	protected $profilesManager;
	
	public function __construct() {
		$this->usersManager = $this->getMapper()->newManager('Acme\Storage\User');
		$this->profilesManager = $this->getMapper()->newManager('Acme\Storage\Profile');
	}
	
	protected function getFilename() {
		return __DIR__ . '/storage.db';
	}
	
	protected function truncate() {
		$this->usersManager->truncate();
		$this->profilesManager->truncate();
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
	
	/*
	 * ONE-TO-ONE
	 */
	
	public function testOneToOneEmpty() {
		$mapper = $this->getMapper();
		$usersManager = $mapper->newManager('Acme\Storage\User');
		$profilesManager = $mapper->newManager('Acme\Storage\Profile');
		
		//truncate tables
		$usersManager->truncate();
		$profilesManager->truncate();
		
		$user = new User();
		$user->name = 'emaphp';
		$user->lastLogin = new \DateTime();
		$user->email = 'emaphp@github.com';
		
		$profile = new Profile();
		$profile->firstname = 'Emmanuel';
		$profile->lastname = 'Antico';
		$profile->gender = 'M';
		$user->profile = $profile;
		
		$userId = $usersManager->save($user, 0);
		$count = $profilesManager->count();
		$this->assertEquals(0, $count);
		
		$mapper->close();
	}
	
	public function testOneToOneUser() {
		$mapper = $this->getMapper();
		$usersManager = $mapper->newManager('Acme\Storage\User');
		$profilesManager = $mapper->newManager('Acme\Storage\Profile');
	
		//truncate tables
		$usersManager->truncate();
		$profilesManager->truncate();
	
		$user = new User();
		$user->name = 'emaphp';
		$user->lastLogin = new \DateTime();
		$user->email = 'emaphp@github.com';
	
		$profile = new Profile();
		$profile->firstname = 'Emmanuel';
		$profile->lastname = 'Antico';
		$profile->gender = 'M';
		$user->profile = $profile;
	
		$userId = $usersManager->save($user);
		$profile = $profilesManager->get(Attr::userId()->eq($userId));
		$this->assertInstanceOf('Acme\Storage\Profile', $profile);
		$this->assertEquals($profile->userId, $userId);
	
		$mapper->close();
	}
	
	public function testOneToOneProfile() {
		$mapper = $this->getMapper();
		$usersManager = $mapper->newManager('Acme\Storage\User');
		$profilesManager = $mapper->newManager('Acme\Storage\Profile');
	
		//truncate tables
		$usersManager->truncate();
		$profilesManager->truncate();
	
		$user = new User();
		$user->name = 'emaphp';
		$user->lastLogin = new \DateTime();
		$user->email = 'emaphp@github.com';
	
		$profile = new Profile();
		$profile->firstname = 'Emmanuel';
		$profile->lastname = 'Antico';
		$profile->gender = 'M';
		$profile->user = $user;
		
		$profileId = $profilesManager->save($profile);
		$this->assertNotNull($profile->user->id);
	
		$mapper->close();
	}
	
	public function testOneToOneDeleteUser() {
		$mapper = $this->getMapper();
		$usersManager = $mapper->newManager('Acme\Storage\User');
		$profilesManager = $mapper->newManager('Acme\Storage\Profile');
	
		//truncate tables
		$usersManager->truncate();
		$profilesManager->truncate();
	
		$user = new User();
		$user->name = 'emaphp';
		$user->lastLogin = new \DateTime();
		$user->email = 'emaphp@github.com';
	
		$profile = new Profile();
		$profile->firstname = 'Emmanuel';
		$profile->lastname = 'Antico';
		$profile->gender = 'M';
		$user->profile = $profile;
	
		$userId = $usersManager->save($user);
		$this->assertEquals(1, $profilesManager->count());
		$usersManager->delete($user);
		$this->assertEquals(0, $profilesManager->count());
	
		$mapper->close();
	}
	
	public function testOneToOneDeleteProfile() {
		$mapper = $this->getMapper();
		$usersManager = $mapper->newManager('Acme\Storage\User');
		$profilesManager = $mapper->newManager('Acme\Storage\Profile');
		
		//truncate tables
		$usersManager->truncate();
		$profilesManager->truncate();
		
		$user = new User();
		$user->name = 'emaphp';
		$user->lastLogin = new \DateTime();
		$user->email = 'emaphp@github.com';
		
		$profile = new Profile();
		$profile->firstname = 'Emmanuel';
		$profile->lastname = 'Antico';
		$profile->gender = 'M';
		$profile->user = $user;
		
		$profileId = $profilesManager->save($profile);
		$this->assertEquals(1, $usersManager->count());
		$profilesManager->delete($profile);
		$this->assertEquals(0, $profilesManager->count());
		$this->assertEquals(1, $usersManager->count());
	}
}