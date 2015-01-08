<?php
namespace eMapper\Attribute;

use eMapper\MapperTest;

abstract class AbstractDynamicStatementTest extends MapperTest {
	public function testfindByPk() {
		$manager = $this->mapper->newManager('Acme\Statement\Category');
		$category = $manager->findByPk(6);
		$this->assertInstanceOf('Acme\Statement\Category', $category->parent);
		$this->assertEquals(2, $category->parent->id);
	}
	
	public function testfindBy() {
		$manager = $this->mapper->newManager('Acme\Statement\Category');
		$category = $manager->findByPk(6);
		$this->assertInternalType('array', $category->subcategories);
		$this->assertCount(3, $category->subcategories);
	}
	
	public function testFindAll() {
		$manager = $this->mapper->newManager('Acme\Statement\Product');
		$product = $manager->findByPk(1);
		$this->assertInternalType('array', $product->categories);
		$this->assertCount(16, $product->categories);
	}
	
	public function testFindByUnique() {
		$manager = $this->mapper->newManager('Acme\Statement\Profile');
		$profile = $manager->findByPk(1);
		$this->assertInstanceOf('Acme\Statement\User', $profile->user);
		$this->assertEquals('jdoe', $profile->user->name);
	}
	
	public function testEqualsUnique() {
		$manager = $this->mapper->newManager('Acme\Statement\Sale');
		$sale = $manager->findByPk(1);
		$this->assertInstanceOf('Acme\Statement\Product', $sale->product);
		$this->assertEquals('PHN00098', $sale->product->code);
	}
	
	public function testEquals() {
		$manager = $this->mapper->newManager('Acme\Statement\Product');
		$product = $manager->findByPk(2);
		$this->assertInternalType('array', $product->sales);
		$this->assertCount(1, $product->sales);
	}
	
	public function testNotEquals() {
		$manager = $this->mapper->newManager('Acme\Statement\Product');
		$product = $manager->findByPk(1);
		$this->assertInternalType('array', $product->notSales);
		$this->assertCount(4, $product->notSales);
	}
	
	public function testNotEqualsUnique() {
		$manager = $this->mapper->newManager('Acme\Statement\Sale');
		$sale = $manager->findByPk(1);
		$this->assertInternalType('array', $sale->otherProducts);
		$this->assertCount(7, $sale->otherProducts);
	}
}