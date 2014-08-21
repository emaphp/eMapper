<?php
namespace eMapper\Attribute;

abstract class AbstractConditionalAttributeTest extends \PHPUnit_Framework_TestCase {
	protected $mapper;
	
	public function setUp() {
		$this->mapper = $this->getMapper();
	}
	
	public function testConditionalAttribute() {
		$product = $this->mapper->type('obj:Acme\Result\Attribute\Good')->query("SELECT * FROM products WHERE product_id = 1");
	
		$this->assertInstanceOf('Acme\Result\Attribute\Good', $product);
		$this->assertEquals(1, $product->id);
		$this->assertFalse($product->refurbished);
		$this->assertNull($product->specialDiscount);
	
		$product = $this->mapper->type('obj:Acme\Result\Attribute\Good')->query("SELECT * FROM products WHERE product_id = 5");
	
		$this->assertInstanceOf('Acme\Result\Attribute\Good', $product);
		$this->assertEquals(5, $product->id);
		$this->assertTrue($product->refurbished);
		$this->assertEquals("Special offer: 50% OFF!!!", $product->specialDiscount);
	}
	
	
	public function testConditionalAttributeList() {
		$products = $this->mapper->type('obj:Acme\Result\Attribute\Good[id]')->query("SELECT * FROM products");
	
		$this->assertInternalType('array', $products);
		$this->assertCount(8, $products);
	
		$this->assertArrayHasKey(1, $products);
		$this->assertArrayHasKey(2, $products);
		$this->assertArrayHasKey(3, $products);
		$this->assertArrayHasKey(4, $products);
		$this->assertArrayHasKey(5, $products);
		$this->assertArrayHasKey(6, $products);
		$this->assertArrayHasKey(7, $products);
		$this->assertArrayHasKey(8, $products);
	
		$this->assertFalse($products[1]->refurbished);
		$this->assertFalse($products[2]->refurbished);
		$this->assertFalse($products[3]->refurbished);
		$this->assertFalse($products[4]->refurbished);
		$this->assertTrue($products[5]->refurbished);
		$this->assertFalse($products[6]->refurbished);
		$this->assertFalse($products[7]->refurbished);
		$this->assertFalse($products[8]->refurbished);
	
		$this->assertNull($products[1]->specialDiscount);
		$this->assertNull($products[2]->specialDiscount);
		$this->assertNull($products[3]->specialDiscount);
		$this->assertNull($products[4]->specialDiscount);
		$this->assertEquals("Special offer: 50% OFF!!!", $products[5]->specialDiscount);
		$this->assertNull($products[6]->specialDiscount);
		$this->assertNull($products[7]->specialDiscount);
		$this->assertNull($products[8]->specialDiscount);
	}
	
	public function tearDown() {
		$this->mapper->close();
	}
}
?>