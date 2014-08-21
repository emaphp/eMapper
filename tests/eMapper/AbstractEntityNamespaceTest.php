<?php
namespace eMapper;

use eMapper\SQL\EntityNamespace;
use Acme\Entity\Product;

abstract class AbstractEntityNamespaceTest extends \PHPUnit_Framework_TestCase {
	public function setUp() {
		$this->build();
		$this->mapper->addEntityNamespace(new EntityNamespace('Acme\Entity\Product'));
	}
	
	public abstract function build();
	
	public function testFindByPk() {
		$product = $this->mapper->execute('products.findByPk', 1);
		$this->assertInstanceOf('Acme\Entity\Product', $product);
		
		$product = $this->mapper->execute('products.findByPk', 0);
		$this->assertNull($product);
	}
	
	public function testFindAll() {
		$products = $this->mapper->execute('products.findAll');
		$this->assertCount(8, $products);
		$this->assertInstanceOf('Acme\Entity\Product', $products[0]);
		
		//test mapping type override
		$products = $this->mapper->type('obj:Acme\Entity\Product[id]')->execute('products.findAll');
		$this->assertCount(8, $products);
		$this->assertArrayNotHasKey(0, $products);
		$this->assertInstanceOf('Acme\Entity\Product', $products[1]);
		$this->assertEquals(1, $products[1]->id);
		
		$products = $this->mapper->index_callback(function ($product) {
			return $product->code;
		})->execute('products.findAll');
		
		$this->assertCount(8, $products);
		$this->assertArrayNotHasKey(0, $products);
		$this->assertInstanceOf('Acme\Entity\Product', $products['PHN00098']);
		$this->assertEquals(5, $products['PHN00098']->id);
		
		$products = $this->mapper->type('obj:Acme\Entity\Product<category>[id]')->execute('products.findAll');
		$this->assertCount(5, $products);
	}
	
	public function testFindByUnique() {
		$product = $this->mapper->execute('products.findById', 1);
		$this->assertInstanceOf('Acme\Entity\Product', $product);
		$this->assertEquals(1, $product->id);
		
		$product = $this->mapper->execute('products.findByCode', 'GFX00067');
		$this->assertInstanceOf('Acme\Entity\Product', $product);
		$this->assertEquals(4, $product->id);
		
		$product = $this->mapper->execute('products.findById', 0);
		$this->assertNull($product);
	}
	
	public function testEqualsUnique() {
		$product = $this->mapper->execute('products.idEquals', 1);
		$this->assertInstanceOf('Acme\Entity\Product', $product);
		$this->assertEquals(1, $product->id);
		
		$product = $this->mapper->execute('products.codeEquals', 'GFX00067');
		$this->assertInstanceOf('Acme\Entity\Product', $product);
		$this->assertEquals(4, $product->id);
		
		$product = $this->mapper->execute('products.idEquals', 0);
		$this->assertNull($product);
	}
	
	public function testNotEqualsUnique() {
		$products = $this->mapper->execute('products.idNotEquals', 1);
		$this->assertCount(7, $products);
		
		$product = $this->mapper->execute('products.codeNotEquals', 'GFX00067');
		$this->assertCount(7, $products);
		
		$products = $this->mapper->execute('products.idNotEquals', 0);
		$this->assertCount(8, $products);
	}
	
	public function testContains() {
		$products = $this->mapper->execute('products.codeContains', 'ND0');
		$this->assertCount(3, $products);
		$this->assertInstanceOf('Acme\Entity\Product', $products[0]);
	}
	
	public function testNotContains() {
		$products = $this->mapper->execute('products.codeNotContains', 'HN');
		$this->assertCount(6, $products);
	}
	
	public function testIContains() {
		$products = $this->mapper->execute('products.categoryIContains', 'hard');
		$this->assertCount(1, $products);
		$this->assertInstanceOf('Acme\Entity\Product', $products[0]);
	}
	
	public function testNotIContains() {
		$products = $this->mapper->execute('products.categoryNotIContains', 'hard');
		$this->assertCount(7, $products);
		$this->assertInstanceOf('Acme\Entity\Product', $products[0]);
	}
	
	public function testIn() {
		$products = $this->mapper->execute('products.idIn', [1, 2, 3, 4]);
		$this->assertCount(4, $products);
		$this->assertInstanceOf('Acme\Entity\Product', $products[0]);
	
		$products = $this->mapper->execute('products.idIn', 1);
		$this->assertCount(1, $products);
		$this->assertInstanceOf('Acme\Entity\Product', $products[0]);
		$this->assertEquals(1, $products[0]->id);
	}
	
	public function testNotIn() {
		$products = $this->mapper->execute('products.idNotIn', [1, 2, 3, 4]);
		$this->assertCount(4, $products);
		$this->assertInstanceOf('Acme\Entity\Product', $products[0]);
		$this->assertEquals(5, $products[0]->id);
		
		$products = $this->mapper->execute('products.idNotIn', 1);
		$this->assertCount(7, $products);
	}
	
	public function testGreaterThan() {
		$products = $this->mapper->execute('products.idGreaterThan', 3);
		$this->assertCount(5, $products);
		
		$products = $this->mapper->execute('products.priceGreaterThan', 100);
		$this->assertCount(7, $products);
	}
	
	public function testNotGreaterThan() {
		$products = $this->mapper->execute('products.idNotGreaterThan', 3);
		$this->assertCount(3, $products);
		
		$products = $this->mapper->execute('products.priceNotGreaterThan', 100);
		$this->assertCount(1, $products);
	}
	
	public function testLessThan() {
		$products = $this->mapper->execute('products.idLessThan', 3);
		$this->assertCount(2, $products);
		
		$products = $this->mapper->execute('products.priceLessThan', 100);
		$this->assertCount(1, $products);
	}
	
	public function testNotLessThan() {
		$products = $this->mapper->execute('products.idNotLessThan', 3);
		$this->assertCount(6, $products);
		
		$products = $this->mapper->execute('products.priceNotLessThan', 100);
		$this->assertCount(7, $products);
	}
	
	public function testStartsWith() {
		$products = $this->mapper->execute('products.codeStartsWith', 'IND');
		$this->assertCount(3, $products);
	}
	
	public function testIStartsWith() {
		$products = $this->mapper->execute('products.codeIStartsWith', 'ind');
		$this->assertCount(3, $products);
	}
	
	public function testNotStartsWith() {
		$products = $this->mapper->execute('products.codeNotStartsWith', 'IND');
		$this->assertCount(5, $products);
	}
	
	public function testNotIStartsWith() {
		$products = $this->mapper->execute('products.codeNotIStartsWith', 'ind');
		$this->assertCount(5, $products);
	}
	
	public function testEndsWith() {
		$products = $this->mapper->execute('products.categoryEndsWith', 's');
		$this->assertCount(6, $products);
	}
	
	public function testIEndsWith() {
		$products = $this->mapper->execute('products.categoryIEndsWith', 's');
		$this->assertCount(6, $products);
	}
	
	public function testNotEndsWith() {
		$products = $this->mapper->execute('products.categoryNotEndsWith', 's');
		$this->assertCount(2, $products);
	}
	
	public function testNotIEndsWith() {
		$products = $this->mapper->execute('products.categoryNotIEndsWith', 's');
		$this->assertCount(2, $products);
	}
	
	public function testIsNull() {
		$products = $this->mapper->execute('products.colorIsNull');
		$this->assertCount(3, $products);
	}
	
	public function testIsNotNull() {
		$products = $this->mapper->execute('products.colorIsNotNull');
		$this->assertCount(5, $products);
	}
	
	public function testBetween() {
		$products = $this->mapper->execute('products.idBetween', 2, 4);
		$this->assertCount(3, $products);
	}
	
	public function testNotBetween() {
		$products = $this->mapper->execute('products.idNotBetween', 2, 4);
		$this->assertCount(5, $products);
	}
	
	public function tearDown() {
		$this->mapper->close();
	}
}
?>