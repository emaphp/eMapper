<?php
namespace eMapper\Reflection;

/**
 * 
 * @author emaphp
 * @group association
 */
class AssociationTest extends \PHPUnit_Framework_TestCase {
	public function testOneToMany() {
		$profile = Profiler::getClassProfile('Acme\Association\Product');
		$assoc = $profile->getAssociations();
		$this->assertInternalType('array', $assoc);
		$this->assertArrayHasKey('sales', $assoc);
		$assoc = $profile->getAssociation('sales');
		$this->assertInstanceOf('eMapper\Reflection\Profile\Association\OneToMany', $assoc);
		$this->assertEquals('Acme\Association\Product', $assoc->getParent());
		$this->assertEquals('Acme\Association\Sale', $assoc->getProfile());
		$this->assertEquals('productId', $assoc->getAttribute()->getValue());
		$this->assertTrue($assoc->isLazy());
		$this->assertEquals('INNER JOIN @@sales _c ON _t.product_id = _c.product_id', $assoc->buildJoin('_c', '_t'));
	}
	
	public function testOneToOne() {
		$profile = Profiler::getClassProfile('Acme\Association\Profile');
		$assoc = $profile->getAssociations();
		$this->assertInternalType('array', $assoc);
		$this->assertArrayHasKey('user', $assoc);
		$assoc = $profile->getAssociation('user');
		$this->assertInstanceOf('eMapper\Reflection\Profile\Association\OneToOne', $assoc);
		$this->assertEquals('Acme\Association\User', $assoc->getProfile());
		$this->assertEquals('Acme\Association\Profile', $assoc->getParent());
		$this->assertEquals('userId', $assoc->getAttribute()->getArgument());
		$this->assertFalse($assoc->isLazy());
		$this->assertEquals('INNER JOIN @@users _c ON _c.user_id = _t.profile_id', $assoc->buildJoin('_c', '_t'));
		
		$profile = Profiler::getClassProfile('Acme\Association\User');
		$assoc = $profile->getAssociations();
		$this->assertInternalType('array', $assoc);
		$this->assertArrayHasKey('profile', $assoc);
		$assoc = $profile->getAssociation('profile');
		$this->assertInstanceOf('eMapper\Reflection\Profile\Association\OneToOne', $assoc);
		$this->assertEquals('Acme\Association\Profile', $assoc->getProfile());
		$this->assertEquals('Acme\Association\User', $assoc->getParent());
		$this->assertEquals('userId', $assoc->getAttribute()->getValue());
		$this->assertTrue($assoc->isLazy());
		$this->assertEquals('INNER JOIN @@profiles _c ON _t.user_id = _c.user_id', $assoc->buildJoin('_c', '_t'));
	}
	
	public function testManyToOne() {
		$profile = Profiler::getClassProfile('Acme\Association\Sale');
		$assoc = $profile->getAssociations();
		$this->assertInternalType('array', $assoc);
		$this->assertArrayHasKey('product', $assoc);
		$this->assertArrayHasKey('user', $assoc);
		
		$assoc = $profile->getAssociation('product');
		$this->assertInstanceOf('eMapper\Reflection\Profile\Association\ManyToOne', $assoc);
		$this->assertEquals('Acme\Association\Product', $assoc->getProfile());
		$this->assertEquals('Acme\Association\Sale', $assoc->getParent());
		$this->assertEquals('productId', $assoc->getAttribute()->getArgument());
		$this->assertFalse($assoc->isLazy());
		$this->assertEquals('INNER JOIN @@products _c ON _t.product_id = _c.product_id', $assoc->buildJoin('_c', '_t'));
		
		$assoc = $profile->getAssociation('user');
		$this->assertInstanceOf('eMapper\Reflection\Profile\Association\ManyToOne', $assoc);
		$this->assertEquals('Acme\Association\User', $assoc->getProfile());
		$this->assertEquals('Acme\Association\Sale', $assoc->getParent());
		$this->assertEquals('userId', $assoc->getAttribute()->getArgument());
		$this->assertTrue($assoc->isLazy());
		$this->assertEquals('INNER JOIN @@users _c ON _t.user_id = _c.user_id', $assoc->buildJoin('_c', '_t'));
	}
	
	public function testManyToMany() {
		$profile = Profiler::getClassProfile('Acme\Association\User');
		$assoc = $profile->getAssociations();
		$this->assertInternalType('array', $assoc);
		$this->assertArrayHasKey('favorites', $assoc);
		
		$assoc = $profile->getAssociation('favorites');
		$this->assertInstanceOf('eMapper\Reflection\Profile\Association\ManyToMany', $assoc);
		$this->assertEquals('Acme\Association\Product', $assoc->getProfile());
		$this->assertEquals('Acme\Association\User', $assoc->getParent());
		$this->assertEquals('usr_id', $assoc->getColumn()->getValue());
		$join = $assoc->getJoinWith();
		$this->assertEquals('favorites', $join->getArgument());
		$this->assertEquals('prd_id', $join->getValue());
		$this->assertTrue($assoc->isLazy());
		
		$this->assertEquals('INNER JOIN @@favorites _c_t ON _c_t.usr_id = _t.user_id INNER JOIN @@products _c ON _c_t.prd_id = _c.product_id', $assoc->buildJoin('_c', '_t'));
	}
}
?>