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
}
?>