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
	
	public function testManyToOne() {
		$manager = $this->mapper->buildManager('Acme\Association\Sale');
		$sale = $manager->findByPk(1);
		$this->assertInstanceOf('Acme\Association\Sale', $sale);
		$product = $sale->getProduct();
		$this->assertInstanceOf('Acme\Association\Product', $product);
		$this->assertEquals(5, $product->getId());
		$user = $sale->getUser();
		$this->assertInstanceOf('eMapper\AssociationManager', $user);
		$user = $user->get();
		$this->assertInstanceOf('Acme\Association\User', $user);
		$this->assertEquals(1, $user->getId());
		
		$sale = $manager->findByPk(2);
		$this->assertInstanceOf('Acme\Association\Sale', $sale);
		$product = $sale->getProduct();
		$this->assertInstanceOf('Acme\Association\Product', $product);
		$this->assertEquals(2, $product->getId());
		$user = $sale->getUser();
		$this->assertInstanceOf('eMapper\AssociationManager', $user);
		$user = $user->get();
		$this->assertInstanceOf('Acme\Association\User', $user);
		$this->assertEquals(5, $user->getId());
	}
	
	public function testOneToMany() {
		$manager = $this->mapper->buildManager('Acme\Association\Product');
		$product = $manager->findByPk(5);
		$sales = $product->getSales();
		$this->assertInstanceOf('eMapper\AssociationManager', $sales);
		$sales = $sales->find();
		$this->assertInternalType('array', $sales);
		$this->assertCount(1, $sales);
		$sale = $sales[0];
		$this->assertInstanceOf('Acme\Association\Sale', $sale);
		$this->assertEquals(1, $sale->getId());
		
		$manager = $this->mapper->buildManager('Acme\Association\Product');
		$product = $manager->findByPk(1);
		$sales = $product->getSales();
		$this->assertInstanceOf('eMapper\AssociationManager', $sales);
		$sales = $sales->find();
		$this->assertInternalType('array', $sales);
		$this->assertCount(0, $sales);
	}
	
	public function testManyToMany() {
		$manager = $this->mapper->buildManager('Acme\Association\User');
		$user = $manager->findByPk(1);
		$favorites = $user->getFavorites();
		$this->assertInstanceOf('eMapper\AssociationManager', $favorites);
		$favorites = $favorites->find();
		$this->assertInternalType('array', $favorites);
		$this->assertCount(2, $favorites);
		$this->assertInstanceOf('Acme\Association\Product', $favorites[0]);
		$this->assertInstanceOf('Acme\Association\Product', $favorites[1]);
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
		
		foreach ($profiles as $profile) {
			$this->assertEquals($profile->getId(), $profile->getUser()->getId());
		}
	}
	
	public function testManyToOneList() {
		$manager = $this->mapper->buildManager('Acme\Association\Sale');
		$sales = $manager->index(Attr::id())->find();
		$this->assertInternalType('array', $sales);
		$this->assertArrayHasKey(1, $sales);
		$this->assertArrayHasKey(2, $sales);
		$this->assertArrayHasKey(3, $sales);
		$this->assertArrayHasKey(4, $sales);
		
		$sale = $sales[1];
		$this->assertInstanceOf('Acme\Association\Product', $sale->getProduct());
		$this->assertEquals(5, $sale->getProduct()->getId());
		$this->assertInstanceOf('eMapper\AssociationManager', $sale->getUser());
		$user = $sale->getUser()->get();
		$this->assertInstanceOf('Acme\Association\User', $user);
		$this->assertEquals(1, $user->getId());
		
		$sale = $sales[2];
		$this->assertInstanceOf('Acme\Association\Product', $sale->getProduct());
		$this->assertEquals(2, $sale->getProduct()->getId());
		$this->assertInstanceOf('eMapper\AssociationManager', $sale->getUser());
		$user = $sale->getUser()->get();
		$this->assertInstanceOf('Acme\Association\User', $user);
		$this->assertEquals(5, $user->getId());
		
		$sale = $sales[3];
		$this->assertInstanceOf('Acme\Association\Product', $sale->getProduct());
		$this->assertEquals(4, $sale->getProduct()->getId());
		$this->assertInstanceOf('eMapper\AssociationManager', $sale->getUser());
		$user = $sale->getUser()->get();
		$this->assertInstanceOf('Acme\Association\User', $user);
		$this->assertEquals(2, $user->getId());
		
		$sale = $sales[4];
		$this->assertInstanceOf('Acme\Association\Product', $sale->getProduct());
		$this->assertEquals(3, $sale->getProduct()->getId());
		$this->assertInstanceOf('eMapper\AssociationManager', $sale->getUser());
		$user = $sale->getUser()->get();
		$this->assertInstanceOf('Acme\Association\User', $user);
		$this->assertEquals(3, $user->getId());
	}
	
	public function testOneToManyList() {
		$manager = $this->mapper->buildManager('Acme\Association\Product');
		$products = $manager->index(Attr::id())->find();
		$this->assertInternalType('array', $products);
		$this->assertCount(8, $products);
		
		$product = $products[1];
		$this->assertInstanceOf('Acme\Association\Product', $product);
		$sales = $product->getSales();
		$this->assertInstanceOf('eMapper\AssociationManager', $sales);
		$sales = $sales->fetch();
		$this->assertInternalType('array', $sales);
		$this->assertEmpty($sales);
		
		$product = $products[2];
		$this->assertInstanceOf('Acme\Association\Product', $product);
		$sales = $product->getSales();
		$this->assertInstanceOf('eMapper\AssociationManager', $sales);
		$sales = $sales->fetch();
		$this->assertInternalType('array', $sales);
		$this->assertCount(1, $sales);
		$this->assertInstanceOf('Acme\Association\Sale', $sales[0]);
		$this->assertEquals(2, $sales[0]->getId());
		
		$product = $products[3];
		$this->assertInstanceOf('Acme\Association\Product', $product);
		$sales = $product->getSales();
		$this->assertInstanceOf('eMapper\AssociationManager', $sales);
		$sales = $sales->fetch();
		$this->assertInternalType('array', $sales);
		$this->assertCount(1, $sales);
		$this->assertInstanceOf('Acme\Association\Sale', $sales[0]);
		$this->assertEquals(4, $sales[0]->getId());
		
		$product = $products[4];
		$this->assertInstanceOf('Acme\Association\Product', $product);
		$sales = $product->getSales();
		$this->assertInstanceOf('eMapper\AssociationManager', $sales);
		$sales = $sales->fetch();
		$this->assertInternalType('array', $sales);
		$this->assertCount(1, $sales);
		$this->assertInstanceOf('Acme\Association\Sale', $sales[0]);
		$this->assertEquals(3, $sales[0]->getId());
		
		$product = $products[5];
		$this->assertInstanceOf('Acme\Association\Product', $product);
		$sales = $product->getSales();
		$this->assertInstanceOf('eMapper\AssociationManager', $sales);
		$sales = $sales->fetch();
		$this->assertInternalType('array', $sales);
		$this->assertCount(1, $sales);
		$this->assertInstanceOf('Acme\Association\Sale', $sales[0]);
		$this->assertEquals(1, $sales[0]->getId());
		
		$product = $products[6];
		$this->assertInstanceOf('Acme\Association\Product', $product);
		$sales = $product->getSales();
		$this->assertInstanceOf('eMapper\AssociationManager', $sales);
		$sales = $sales->fetch();
		$this->assertInternalType('array', $sales);
		$this->assertEmpty($sales);
		
		$product = $products[7];
		$this->assertInstanceOf('Acme\Association\Product', $product);
		$sales = $product->getSales();
		$this->assertInstanceOf('eMapper\AssociationManager', $sales);
		$sales = $sales->fetch();
		$this->assertInternalType('array', $sales);
		$this->assertEmpty($sales);
		
		$product = $products[8];
		$this->assertInstanceOf('Acme\Association\Product', $product);
		$sales = $product->getSales();
		$this->assertInstanceOf('eMapper\AssociationManager', $sales);
		$sales = $sales->fetch();
		$this->assertInternalType('array', $sales);
		$this->assertEmpty($sales);
	}
	
	public function testManyToManyList() {
		$manager = $this->mapper->buildManager('Acme\Association\User');
		$users = $manager->index(Attr::id())->find();
		$this->assertInternalType('array', $users);
		$this->assertCount(5, $users);
		
		$user = $users[1];
		$this->assertInstanceOf('Acme\Association\User', $user);
		$favorites = $user->getFavorites();
		$this->assertInstanceOf('eMapper\AssociationManager', $favorites);
		$favorites = $favorites->fetch();
		$this->assertInternalType('array', $favorites);
		$this->assertCount(2, $favorites);
		$this->assertInstanceOf('Acme\Association\Product', $favorites[0]);
		$this->assertInstanceOf('Acme\Association\Product', $favorites[1]);
		
		$user = $users[2];
		$this->assertInstanceOf('Acme\Association\User', $user);
		$favorites = $user->getFavorites();
		$this->assertInstanceOf('eMapper\AssociationManager', $favorites);
		$favorites = $favorites->fetch();
		$this->assertInternalType('array', $favorites);
		$this->assertEmpty($favorites);
		
		$user = $users[3];
		$this->assertInstanceOf('Acme\Association\User', $user);
		$favorites = $user->getFavorites();
		$this->assertInstanceOf('eMapper\AssociationManager', $favorites);
		$favorites = $favorites->fetch();
		$this->assertInternalType('array', $favorites);
		$this->assertCount(2, $favorites);
		$this->assertInstanceOf('Acme\Association\Product', $favorites[0]);
		$this->assertInstanceOf('Acme\Association\Product', $favorites[1]);
		
		$user = $users[4];
		$this->assertInstanceOf('Acme\Association\User', $user);
		$favorites = $user->getFavorites();
		$this->assertInstanceOf('eMapper\AssociationManager', $favorites);
		$favorites = $favorites->fetch();
		$this->assertInternalType('array', $favorites);
		$this->assertCount(1, $favorites);
		$this->assertInstanceOf('Acme\Association\Product', $favorites[0]);
		
		$user = $users[5];
		$this->assertInstanceOf('Acme\Association\User', $user);
		$favorites = $user->getFavorites();
		$this->assertInstanceOf('eMapper\AssociationManager', $favorites);
		$favorites = $favorites->fetch();
		$this->assertInternalType('array', $favorites);
		$this->assertEmpty($favorites);
	}
	
	public function testDebugAssociation() {
		$manager = $this->mapper->buildManager('Acme\Association\User');
		$user = $manager
		->debug(function ($query) {
			static $index = 0;
			static $values = [
				'SELECT _t.* FROM users _t WHERE _t.user_id = 1',
				'SELECT _t.* FROM profiles _t WHERE _t.user_id = 1',
			];
			
			$this->assertEquals($values[$index++], $query);
		})
		->findByPk(1);
		
		$manager = $this->mapper->buildManager('Acme\Association\Category');
		$category = $manager
		->debug(function ($query) {
			static $index = 0;
			static $values = [
				'SELECT _t.* FROM categories _t WHERE _t.name = \'Tango\'',
				'SELECT _t.* FROM categories _t WHERE _t.category_id = 2',
				'SELECT _t.* FROM categories _t WHERE _t.parent_id = 8',
			];
			
			$this->assertEquals($values[$index++], $query);
		})
		->get(Attr::name()->eq('Tango'));
	}
}
?>