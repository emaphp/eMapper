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
		$this->assertCount(5, $products);
		$this->assertInstanceOf('Acme\Entity\Product', $products[0]);
		
		//test mapping type override
		$products = $this->mapper->type('obj:Acme\Entity\Product[id]')->execute('products.findAll');
		$this->assertCount(5, $products);
		$this->assertArrayNotHasKey(0, $products);
		$this->assertInstanceOf('Acme\Entity\Product', $products[1]);
		$this->assertEquals(1, $products[1]->id);
		
		$products = $this->mapper->index_callback(function ($product) {
			return $product->code;
		})->execute('products.findAll');
		
		$this->assertCount(5, $products);
		$this->assertArrayNotHasKey(0, $products);
		$this->assertInstanceOf('Acme\Entity\Product', $products['PHN00098']);
		$this->assertEquals(5, $products['PHN00098']->id);
		
		$products = $this->mapper->type('obj:Acme\Entity\Product<category>[id]')->execute('products.findAll');
		$this->assertCount(3, $products);
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
		$this->assertCount(4, $products);
		
		$product = $this->mapper->execute('products.codeNotEquals', 'GFX00067');
		$this->assertCount(4, $products);
		
		$products = $this->mapper->execute('products.idNotEquals', 0);
		$this->assertCount(5, $products);
	}
}
?>