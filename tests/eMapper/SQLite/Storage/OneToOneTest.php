<?php
namespace eMapper\SQLite\Storage;

use eMapper\SQLite\StorageTest;
use Acme\Storage\User;
use Acme\Storage\Profile;
use Acme\Storage\Person;
use Acme\Storage\Address;

/**
 * @group storage
 * @group sqlite
 */
class OneToOneTest extends StorageTest {
	public function testOneToOneEmpty() {
		$this->truncateTable('users');
		$this->truncateTable('profiles');
	
		$mapper = $this->getMapper();
		$manager = $mapper->newManager('Acme\Storage\User');
		
		$user = new User();
		$user->name = 'emaphp';
		$user->lastLogin = new \DateTime();
		$user->email = 'emaphp@github.com';
		
		$profile = new Profile();
		$profile->firstname = 'Emmanuel';
		$profile->lastname = 'Antico';
		$profile->gender = 'M';
		$user->profile = $profile;
		
		$userId = $manager->save($user, 0);
		$this->assertEquals(0, $mapper->newManager('Acme\Storage\Profile')->count());
		
		$mapper->close();
	}
	
	public function testCreateUser() {
		$this->truncateTable('users');
		$this->truncateTable('profiles');
		
		$mapper = $this->getMapper();
		$manager = $mapper->newManager('Acme\Storage\User');
		
		$user = new User();
		$user->name = 'emaphp';
		$user->lastLogin = new \DateTime();
		$user->email = 'emaphp@github.com';
		
		$profile = new Profile();
		$profile->firstname = 'Emmanuel';
		$profile->lastname = 'Antico';
		$profile->gender = 'M';
		
		$user->profile = $profile;
		$userId = $manager->save($user);
		
		//check values
		$this->assertEquals(1, $manager->count());
		$profiles = $mapper->newManager('Acme\Storage\Profile');
		$this->assertEquals(1, $profiles->count());
		$profile = $profiles->get();
		$this->assertEquals($userId, $profile->userId);
		
		$mapper->close();
	}
	
	public function testCreateProfile() {
		$this->truncateTable('users');
		$this->truncateTable('profiles');
	
		$mapper = $this->getMapper();
		$manager = $mapper->newManager('Acme\Storage\Profile');
	
		$profile = new Profile();
		$profile->firstname = 'Emmanuel';
		$profile->lastname = 'Antico';
		$profile->gender = 'M';
		
		$user = new User();
		$user->name = 'emaphp';
		$user->lastLogin = new \DateTime();
		$user->email = 'emaphp@github.com';
	
		$profile->user = $user;
		$profileId = $manager->save($profile);
	
		//check values
		$this->assertEquals(1, $manager->count());
		$users = $mapper->newManager('Acme\Storage\User');
		$this->assertEquals(1, $users->count());
		$user = $users->get();
		$this->assertEquals($profileId, $user->profile->id);
	
		$mapper->close();
	}
	
	public function testUpdateUser() {
		$this->truncateTable('users');
		$this->truncateTable('profiles');
		
		$mapper = $this->getMapper();
		$manager = $mapper->newManager('Acme\Storage\User');
		
		$user = new User();
		$user->name = 'emaphp';
		$user->lastLogin = new \DateTime();
		$user->email = 'emaphp@github.com';
		
		$profile = new Profile();
		$profile->firstname = 'Emmanuel';
		$profile->lastname = 'Antico';
		$profile->gender = 'M';
		
		$user->profile = $profile;
		$userId = $manager->save($user);
		
		//make changes
		$user = $manager->findByPk($userId);
		$user->name = 'ema';
		$manager->save($user);
		
		//check values
		$user = $manager->findByPk($userId);
		$this->assertEquals('ema', $user->name);
		$this->assertInstanceOf('Acme\Storage\Profile', $user->profile);
		$this->assertEquals('Emmanuel', $user->profile->firstname);
		
		$mapper->close();
	}
	
	public function testUpdateProfile() {
		$this->truncateTable('users');
		$this->truncateTable('profiles');
	
		$mapper = $this->getMapper();
		$manager = $mapper->newManager('Acme\Storage\Profile');
	
		$profile = new Profile();
		$profile->firstname = 'Emmanuel';
		$profile->lastname = 'Antico';
		$profile->gender = 'M';
		
		$user = new User();
		$user->name = 'emaphp';
		$user->lastLogin = new \DateTime();
		$user->email = 'emaphp@github.com';
	
		$profile->user = $user;
		$profileId = $manager->save($profile);
		
		//make changes
		$profile = $manager->findByPk($profileId);
		$profile->lastname = 'Goldberg';
		$manager->save($profile);
		
		//check values
		$profile = $manager->findByPk($profileId);
		$this->assertEquals('Goldberg', $profile->lastname);
		$this->assertInstanceOf('Acme\Storage\User', $profile->user);
		$this->assertEquals('emaphp', $profile->user->name);
		
		$mapper->close();
	}
	
	public function testUpdateProfileFromUser() {
		$this->truncateTable('users');
		$this->truncateTable('profiles');
		
		$mapper = $this->getMapper();
		$manager = $mapper->newManager('Acme\Storage\User');
		
		$user = new User();
		$user->name = 'emaphp';
		$user->lastLogin = new \DateTime();
		$user->email = 'emaphp@github.com';
		
		$profile = new Profile();
		$profile->firstname = 'Emmanuel';
		$profile->lastname = 'Antico';
		$profile->gender = 'M';
		
		$user->profile = $profile;
		$userId = $manager->save($user);
		
		//make changes
		$user = $manager->findByPk($userId);
		$user->profile->lastname = 'Goldberg';
		$manager->save($user);
		
		//check values
		$profile = $mapper->newManager('Acme\Storage\Profile')->get();
		$this->assertEquals('Goldberg', $profile->lastname);
		
		$mapper->close();
	}
	
	public function testUpdateUserFromProfile() {
		$this->truncateTable('users');
		$this->truncateTable('profiles');
		
		$mapper = $this->getMapper();
		$manager = $mapper->newManager('Acme\Storage\Profile');
		
		$profile = new Profile();
		$profile->firstname = 'Emmanuel';
		$profile->lastname = 'Antico';
		$profile->gender = 'M';
		
		$user = new User();
		$user->name = 'emaphp';
		$user->lastLogin = new \DateTime();
		$user->email = 'emaphp@github.com';
		
		$profile->user = $user;
		$profileId = $manager->save($profile);
		
		//make changes
		$profile = $manager->findByPk($profileId);
		$profile->user->name = 'ema';
		$manager->save($profile);
		
		//check values
		$user = $mapper->newManager('Acme\Storage\User')->get();
		$this->assertEquals('ema', $user->name);
		
		$mapper->close();
	}
	
	public function testDeleteUser() {
		$this->truncateTable('users');
		$this->truncateTable('profiles');
		
		$mapper = $this->getMapper();
		$manager = $mapper->newManager('Acme\Storage\User');
		
		$user = new User();
		$user->name = 'emaphp';
		$user->lastLogin = new \DateTime();
		$user->email = 'emaphp@github.com';
		
		$profile = new Profile();
		$profile->firstname = 'Emmanuel';
		$profile->lastname = 'Antico';
		$profile->gender = 'M';
		
		$user->profile = $profile;
		$userId = $manager->save($user);
		
		//make changes
		$manager->delete($user);
		
		//check values
		$this->assertEquals(0, $mapper->newManager('Acme\Storage\Profile')->count());
		$this->assertEquals(0, $manager->count());
		
		$mapper->close();
	}
	
	public function testDeleteProfile() {
		$this->truncateTable('users');
		$this->truncateTable('profiles');
		
		$mapper = $this->getMapper();
		$manager = $mapper->newManager('Acme\Storage\Profile');
		
		$profile = new Profile();
		$profile->firstname = 'Emmanuel';
		$profile->lastname = 'Antico';
		$profile->gender = 'M';
		
		$user = new User();
		$user->name = 'emaphp';
		$user->lastLogin = new \DateTime();
		$user->email = 'emaphp@github.com';
		$profile->user = $user;
		$manager->save($profile);
		
		//make changes
		$manager->delete($profile);
		
		$this->assertEquals(0, $manager->count());
		$this->assertEquals(1, $mapper->newManager('Acme\Storage\User')->count());
		
		$mapper->close();
	}
	
	public function testDeleteAddress() {
		$this->truncateTable('people');
		$this->truncateTable('addresses');
		
		$mapper = $this->getMapper();
		$manager = $mapper->newManager('Acme\Storage\Person');
		
		$person = new Person();
		$person->name = 'Lazaro';
		$person->lastname = 'Baez';
		
		$address = new Address();
		$address->street = 'Balcarce';
		$address->number = 54;
		$address->city = 'Capital Federal';
		$person->address = $address;
		
		$personId = $manager->save($person);
		$this->assertInternalType('integer', $personId);
		
		$addresses = $mapper->newManager('Acme\Storage\Address');
		$this->assertEquals(1, $addresses->count());
		
		$addresses->delete($address);
		$this->assertEquals(0, $addresses->count());
		
		$person = $manager->findByPk($personId);
		$this->assertNull($person->addressId);
		$this->assertNull($person->address);
		
		$mapper->close();
	}
}
