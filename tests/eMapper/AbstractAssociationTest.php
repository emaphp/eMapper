<?php
namespace eMapper;

use eMapper\Query\Attr;

abstract class AbstractAssociationTest extends \PHPUnit_Framework_TestCase {
	protected $mapper;
	
	public function testOneToOne() {
		//profiles
		$manager = $this->mapper->buildManager('Acme\Association\Profile');
		$profile = $manager->findByPk(1);
		$this->assertInstanceOf('Acme\Association\Profile', $profile);
		$user = $profile->getUser();
		$this->assertInstanceOf('Acme\Association\User', $user);
		$this->assertEquals(1, $user->getId());
		$this->assertEquals('jdoe', $user->getName());
		$this->assertNull($user->getFavorites());
		$this->assertNull($user->getProfile());
		
		$profile = $manager->findByPk(2);
		$this->assertInstanceOf('Acme\Association\Profile', $profile);
		$user = $profile->getUser();
		$this->assertInstanceOf('Acme\Association\User', $user);
		$this->assertEquals(2, $user->getId());
		
		//users
		$manager = $this->mapper->buildManager('Acme\Association\User');
		$user = $manager->findByPk(1);
		$this->assertInstanceOf('Acme\Association\User', $user);
		$profile = $user->getProfile();
		$this->assertInstanceOf('eMapper\AssociationManager', $profile);
		$profile = $profile->get();
		$this->assertInstanceOf('Acme\Association\Profile', $profile);
		$this->assertEquals(1, $profile->getId());
	}
	
	public function testOneToOneList() {
		$manager = $this->mapper->buildManager('Acme\Association\Profile');
		$profiles = $manager->find();
		$this->assertInternalType('array', $profiles);
		$this->assertCount(5, $profiles);
		$profile = $profiles[0];
		$this->assertEquals($profile->getId(), $profile->getUser()->getId());
		
		$profiles = $manager->index(Attr::id())->find();
		$this->assertInternalType('array', $profiles);
		$this->assertCount(5, $profiles);
		$this->assertArrayHasKey(1, $profiles);
		$this->assertArrayHasKey(2, $profiles);
		$this->assertArrayHasKey(3, $profiles);
		$this->assertArrayHasKey(4, $profiles);
		$this->assertArrayHasKey(5, $profiles);
		$profile = $profiles[5];
		$this->assertEquals($profile->getId(), $profile->getUser()->getId());
	}
}
?>