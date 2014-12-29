<?php
namespace eMapper\Reflection;

/**
 * 
 * @author emaphp
 * @group reflection
 * @group association
 */
class AssociationTest extends \PHPUnit_Framework_TestCase {
	public function testOneToMany() {
		$profile = Profiler::getClassProfile('Acme\Association\Product');
		$assoc = $profile->getAssociations();
		$this->assertInternalType('array', $assoc);
		$this->assertArrayHasKey('sales', $assoc);
		$assoc = $profile->getAssociation('sales');
		$this->assertInstanceOf('eMapper\ORM\Association\OneToMany', $assoc);
		$this->assertEquals('Acme\Association\Product', $assoc->getParentClass());
		$this->assertEquals('Acme\Association\Sale', $assoc->getEntityClass());
		$this->assertEquals('productId', $assoc->getAttribute()->getValue());
		$this->assertTrue($assoc->isLazy());
	}
	
	public function testOneToOne() {
		$profile = Profiler::getClassProfile('Acme\Association\Profile');
		$assoc = $profile->getAssociations();
		$this->assertInternalType('array', $assoc);
		$this->assertArrayHasKey('user', $assoc);
		$assoc = $profile->getAssociation('user');
		$this->assertInstanceOf('eMapper\ORM\Association\OneToOne', $assoc);
		$this->assertEquals('Acme\Association\User', $assoc->getEntityClass());
		$this->assertEquals('Acme\Association\Profile', $assoc->getParentClass());
		$this->assertEquals('userId', $assoc->getAttribute()->getArgument());
		$this->assertFalse($assoc->isLazy());
		
		$profile = Profiler::getClassProfile('Acme\Association\User');
		$assoc = $profile->getAssociations();
		$this->assertInternalType('array', $assoc);
		$this->assertArrayHasKey('profile', $assoc);
		$assoc = $profile->getAssociation('profile');
		$this->assertInstanceOf('eMapper\ORM\Association\OneToOne', $assoc);
		$this->assertEquals('Acme\Association\Profile', $assoc->getEntityClass());
		$this->assertEquals('Acme\Association\User', $assoc->getParentClass());
		$this->assertEquals('userId', $assoc->getAttribute()->getValue());
		$this->assertTrue($assoc->isLazy());
	}
	
	public function testManyToOne() {
		$profile = Profiler::getClassProfile('Acme\Association\Sale');
		$assoc = $profile->getAssociations();
		$this->assertInternalType('array', $assoc);
		$this->assertArrayHasKey('product', $assoc);
		$this->assertArrayHasKey('user', $assoc);
		
		$assoc = $profile->getAssociation('product');
		$this->assertInstanceOf('eMapper\ORM\Association\ManyToOne', $assoc);
		$this->assertEquals('Acme\Association\Product', $assoc->getEntityClass());
		$this->assertEquals('Acme\Association\Sale', $assoc->getParentClass());
		$this->assertEquals('productId', $assoc->getAttribute()->getArgument());
		$this->assertFalse($assoc->isLazy());
		
		$assoc = $profile->getAssociation('user');
		$this->assertInstanceOf('eMapper\ORM\Association\ManyToOne', $assoc);
		$this->assertEquals('Acme\Association\User', $assoc->getEntityClass());
		$this->assertEquals('Acme\Association\Sale', $assoc->getParentClass());
		$this->assertEquals('userId', $assoc->getAttribute()->getArgument());
		$this->assertTrue($assoc->isLazy());
	}
	
	public function testManyToMany() {
		$profile = Profiler::getClassProfile('Acme\Association\User');
		$assoc = $profile->getAssociations();
		$this->assertInternalType('array', $assoc);
		$this->assertArrayHasKey('favorites', $assoc);
		
		$assoc = $profile->getAssociation('favorites');
		$this->assertInstanceOf('eMapper\ORM\Association\ManyToMany', $assoc);
		$this->assertEquals('Acme\Association\Product', $assoc->getEntityClass());
		$this->assertEquals('Acme\Association\User', $assoc->getParentClass());
		$join = $assoc->getJoin();
		$this->assertEquals('usr_id,prd_id', $join->getArgument());
		$this->assertEquals('favorites', $join->getValue());
		$this->assertTrue($assoc->isLazy());
	}
}