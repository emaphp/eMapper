<?php
namespace eMapper;

use eMapper\MySQL\MySQLConfig;
use eMapper\Query\Attr;
use eMapper\Query\Q;

/**
 * 
 * @author emaphp
 * @group new
 */
abstract class AbstractAssociationQueryTest extends \PHPUnit_Framework_TestCase {
	protected $mapper;
	
	public function testOneToOne() {
		$manager = $this->mapper->buildManager('Acme\Association\Profile');
		$profile = $manager->get(Attr::user__name()->eq('okenobi'));
		$this->assertInstanceOf('Acme\Association\Profile', $profile);
		$this->assertEquals(2, $profile->getId());
		$this->assertEquals('Obi', $profile->getName());
		$this->assertInstanceOf('Acme\Association\User', $profile->getUser());
		$this->assertEquals(2, $profile->getUser()->getId());
		$this->assertEquals('okenobi', $profile->getUser()->getName());
		
		$manager = $this->mapper->buildManager('Acme\Association\User');
		$user = $manager->get(Attr::profile__name()->eq('Ishmael'));
		$this->assertEquals(5, $user->getId());
		$this->assertEquals('ishmael', $user->getName());
		$this->assertInstanceOf('eMapper\AssociationManager', $user->getProfile());
		$profile = $user->getProfile()->fetch();
		$this->assertInstanceOf('Acme\Association\Profile', $profile);
		$this->assertEquals(5, $profile->getId());
		$this->assertEquals('Ishmael', $profile->getName());
	}
	
	public function testOneToMany() {
		$manager = $this->mapper->buildManager('Acme\Association\Product');
		$products = $manager->index(Attr::id())->find(Attr::sales__discount()->gte(0.15));
		$this->assertInternalType('array', $products);
		$this->assertCount(2, $products);
		$this->assertArrayHasKey(5, $products);
		$this->assertArrayHasKey(2, $products);
		$this->assertInstanceOf('Acme\Association\Product', $products[2]);
		$this->assertInstanceOf('Acme\Association\Product', $products[5]);
	}
	
	public function testManyToOne() {
		$manager = $this->mapper->buildManager('Acme\Association\Sale');
		$sales = $manager->index(Attr::id())->find(Attr::product__code()->startswith('IND'));
		$this->assertInternalType('array', $sales);
		$this->assertCount(2, $sales);
		$this->assertArrayHasKey(2, $sales);
		$this->assertArrayHasKey(4, $sales);
		$this->assertInstanceOf('Acme\Association\Sale', $sales[2]);
		$this->assertInstanceOf('Acme\Association\Sale', $sales[4]);
	}
	
	public function testManyToMany() {
		$manager = $this->mapper->buildManager('Acme\Association\User');
		$users = $manager->index(Attr::id())
		->find(Q::where(Attr::favorites__code()->startswith('IND'),
						Attr::favorites__code()->startswith('SOF')));
		$this->assertInternalType('array', $users);
		$this->assertCount(2, $users);
		$this->assertArrayHasKey(3, $users);
		$this->assertArrayHasKey(4, $users);
	}
	
	public function testParentAssociationDepth() {
		$manager = $this->mapper->buildManager('Acme\Association\Category');
		$categories = $manager
		->index(Attr::id())
		->depth(0)
		->find(Attr::parent__parent__name()->eq('Technology'));
		$this->assertCount(5, $categories);
		$this->assertArrayHasKey(9, $categories);
		$this->assertArrayHasKey(10, $categories);
		$this->assertArrayHasKey(11, $categories);
		$this->assertArrayHasKey(12, $categories);
		$this->assertArrayHasKey(13, $categories);
		
		//test left joins
		$categories = $manager
		->index(Attr::id())
		->depth(0)
		->filter(Q::where(Attr::parent__parent__name()->eq('Technology'),
						  Attr::parent__name()->eq('Music')))
		->find();
		$this->assertCount(8, $categories);
		$this->assertArrayHasKey(6, $categories);
		$this->assertArrayHasKey(7, $categories);
		$this->assertArrayHasKey(8, $categories);
		$this->assertArrayHasKey(9, $categories);
		$this->assertArrayHasKey(10, $categories);
		$this->assertArrayHasKey(11, $categories);
		$this->assertArrayHasKey(12, $categories);
		$this->assertArrayHasKey(13, $categories);
		
		$categories = $manager
		->index(Attr::id())
		->depth(0)
		->filter(Q::where(Attr::parent__parent__name()->eq('Technology'),
						  Attr::parent__name()->eq('Music'),
						  Attr::id()->eq(3)))
		->find();
		$this->assertCount(9, $categories);
		$this->assertArrayHasKey(3, $categories);
		$this->assertArrayHasKey(6, $categories);
		$this->assertArrayHasKey(7, $categories);
		$this->assertArrayHasKey(8, $categories);
		$this->assertArrayHasKey(9, $categories);
		$this->assertArrayHasKey(10, $categories);
		$this->assertArrayHasKey(11, $categories);
		$this->assertArrayHasKey(12, $categories);
		$this->assertArrayHasKey(13, $categories);
	}
	
	public function testChildAssocationDepth() {
		$manager = $this->mapper->buildManager('Acme\Association\Category');
		$parent = $manager->depth(0)->get(Attr::subcategories__subcategories__name()->eq('House'));
		$this->assertInstanceOf('Acme\Association\Category', $parent);
		$this->assertEquals('Music', $parent->getName());
		
		$categories = $manager
		->index(Attr::id())
		->depth(0)
		->filter(Q::where(Attr::subcategories__subcategories__name()->eq('House'),
						  Attr::subcategories__name()->eq('Operating Systems')))
		->find();
		$this->assertCount(2, $categories);
		$this->assertArrayHasKey(2, $categories);
		$this->assertArrayHasKey(5, $categories);
		
		$categories = $manager
		->index(Attr::id())
		->depth(0)
		->filter(Q::where(Attr::subcategories__subcategories__name()->eq('House'),
						  Attr::subcategories__name()->eq('Operating Systems'),
						  Attr::id()->eq(3)))
		->find();
		
		$this->assertCount(3, $categories);
		$this->assertArrayHasKey(2, $categories);
		$this->assertArrayHasKey(3, $categories);
		$this->assertArrayHasKey(5, $categories);
	}
}
?>