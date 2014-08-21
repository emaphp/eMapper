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
		$this->assertEquals('Acme\Association\Product', $assoc->getParent()->getName());
		$this->assertEquals('Acme\Association\Sale', $assoc->getProfile()->getName());
		$this->assertEquals('product_id', $assoc->getForeignKey());
		$this->assertEquals('product', $assoc->getReversedBy());
		$this->assertTrue($assoc->isLazy());
		$this->assertEquals('INNER JOIN @@products _c ON _t.product_id = _c.product_id', $assoc->buildJoin('_c', '_t'));
	}
	
	public function testOneToOne() {
		$profile = Profiler::getClassProfile('Acme\Association\Profile');
		$assoc = $profile->getAssociations();
		$this->assertInternalType('array', $assoc);
		$this->assertArrayHasKey('user', $assoc);
		$assoc = $profile->getAssociation('user');
		$this->assertInstanceOf('eMapper\Reflection\Profile\Association\OneToOne', $assoc);
		$this->assertEquals('Acme\Association\User', $assoc->getProfile()->getName());
		$this->assertEquals('Acme\Association\Profile', $assoc->getParent()->getName());
		$this->assertEquals('user_id', $assoc->getColumn());
		$this->assertFalse($assoc->isLazy());
		$this->assertEquals('INNER JOIN @@profiles _c ON _c.user_id = _t.user_id', $assoc->buildJoin('_c', '_t'));
		
		$profile = Profiler::getClassProfile('Acme\Association\User');
		$assoc = $profile->getAssociations();
		$this->assertInternalType('array', $assoc);
		$this->assertArrayHasKey('profile', $assoc);
		$assoc = $profile->getAssociation('profile');
		$this->assertInstanceOf('eMapper\Reflection\Profile\Association\OneToOne', $assoc);
		$this->assertEquals('Acme\Association\Profile', $assoc->getProfile()->getName());
		$this->assertEquals('Acme\Association\User', $assoc->getParent()->getName());
		$this->assertEquals('user_id', $assoc->getForeignKey());
		$this->assertTrue($assoc->isLazy());
		$this->assertEquals('INNER JOIN @@users _c ON _t.user_id = _c.user_id', $assoc->buildJoin('_c', '_t'));
	}
	
	public function testManyToOne() {
		$profile = Profiler::getClassProfile('Acme\Association\Sale');
		$assoc = $profile->getAssociations();
		$this->assertInternalType('array', $assoc);
		$this->assertArrayHasKey('product', $assoc);
		$this->assertArrayHasKey('user', $assoc);
		
		$assoc = $profile->getAssociation('product');
		$this->assertInstanceOf('eMapper\Reflection\Profile\Association\ManyToOne', $assoc);
		$this->assertEquals('Acme\Association\Product', $assoc->getProfile()->getName());
		$this->assertEquals('Acme\Association\Sale', $assoc->getParent()->getName());
		$this->assertEquals('product_id', $assoc->getColumn());
		$this->assertFalse($assoc->isLazy());
		$this->assertEquals('INNER JOIN @@sales _c ON _c.product_id = _t.product_id', $assoc->buildJoin('_c', '_t'));
		
		$assoc = $profile->getAssociation('user');
		$this->assertInstanceOf('eMapper\Reflection\Profile\Association\ManyToOne', $assoc);
		$this->assertEquals('Acme\Association\User', $assoc->getProfile()->getName());
		$this->assertEquals('Acme\Association\Sale', $assoc->getParent()->getName());
		$this->assertEquals('user_id', $assoc->getColumn());
		$this->assertTrue($assoc->isLazy());
		$this->assertEquals('INNER JOIN @@sales _c ON _c.user_id = _t.user_id', $assoc->buildJoin('_c', '_t'));
	}
	
	public function testManyToMany() {
		$profile = Profiler::getClassProfile('Acme\Association\User');
		$assoc = $profile->getAssociations();
		$this->assertInternalType('array', $assoc);
		$this->assertArrayHasKey('favorites', $assoc);
		
		$assoc = $profile->getAssociation('favorites');
		$this->assertInstanceOf('eMapper\Reflection\Profile\Association\ManyToMany', $assoc);
		$this->assertEquals('Acme\Association\Product', $assoc->getProfile()->getName());
		$this->assertEquals('Acme\Association\User', $assoc->getParent()->getName());
		$this->assertEquals('usr_id', $assoc->getForeignKey());
		$join = $assoc->getJoinWith();
		$this->assertEquals('favorites', $join->getArgument());
		$this->assertEquals('prd_id', $join->getValue());
		$this->assertTrue($assoc->isLazy());
		
		$this->assertEquals('INNER JOIN @@favorites _c__t ON _c__t.prd_id = _t.product_id INNER JOIN @@users _c ON _c__t.usr_id = _c.user_id', $assoc->buildJoin('_c', '_t'));
	}
}
?>