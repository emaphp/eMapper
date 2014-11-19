<?php
namespace eMapper\Mapper\ObjectMapper;

use eMapper\MapperTest;

abstract class AbstractEntityTest extends MapperTest {
	public function testRow() {
		$product = $this->mapper->type('obj:Acme\Entity\Product')
		->query("SELECT * FROM products WHERE product_id = 1");
	
		$this->assertInstanceOf('Acme\Entity\Product', $product);
	
		$this->assertInternalType('integer', $product->id);
		$this->assertEquals(1, $product->id);
	
		$this->assertInternalType('string', $product->code);
		$this->assertEquals('IND00054', $product->code);
	
		$this->assertInternalType('string', $product->getCategory());
		$this->assertEquals('Clothes', $product->getCategory());
	
		$this->assertInstanceOf('Acme\RGBColor', $product->color);
	}
	
	public function testList() {
		$products = $this->mapper->type('obj:Acme\Entity\Product[]')
		->query("SELECT * FROM products ORDER BY product_id ASC");
	
		$this->assertInternalType('array', $products);
		$this->assertCount(8, $products);
	
		$this->assertArrayHasKey(0, $products);
		$this->assertArrayHasKey(1, $products);
		$this->assertArrayHasKey(2, $products);
		$this->assertArrayHasKey(3, $products);
		$this->assertArrayHasKey(4, $products);
		$this->assertArrayHasKey(5, $products);
		$this->assertArrayHasKey(6, $products);
		$this->assertArrayHasKey(7, $products);
	
		$product = $products[0];
		$this->assertInstanceOf('Acme\Entity\Product', $product);
	
		$this->assertInternalType('integer', $product->id);
		$this->assertEquals(1, $product->id);
	
		$this->assertInternalType('string', $product->code);
		$this->assertEquals('IND00054', $product->code);
	
		$this->assertInternalType('string', $product->getCategory());
		$this->assertEquals('Clothes', $product->getCategory());
	
		$this->assertInstanceOf('Acme\RGBColor', $product->color);
	}
	
	public function testIndexedList() {
		$products = $this->mapper->type('obj:Acme\Entity\Product[id]')->query("SELECT * FROM products ORDER BY product_id ASC");
	
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
	
		$product = $products[1];
		$this->assertInstanceOf('Acme\Entity\Product', $product);
	
		$this->assertInternalType('integer', $product->id);
		$this->assertEquals(1, $product->id);
	
		$this->assertInternalType('string', $product->code);
		$this->assertEquals('IND00054', $product->code);
	
		$this->assertInternalType('string', $product->getCategory());
		$this->assertEquals('Clothes', $product->getCategory());
	
		$this->assertInstanceOf('Acme\RGBColor', $product->color);
	}
	
	public function testCustomIndexList() {
		$products = $this->mapper->type('obj:Acme\Entity\Product[id:string]')
		->query("SELECT * FROM products ORDER BY product_id ASC");
	
		$this->assertInternalType('array', $products);
		$this->assertCount(8, $products);
	
		$this->assertArrayHasKey('1', $products);
		$this->assertArrayHasKey('2', $products);
		$this->assertArrayHasKey('3', $products);
		$this->assertArrayHasKey('4', $products);
		$this->assertArrayHasKey('5', $products);
		$this->assertArrayHasKey('6', $products);
		$this->assertArrayHasKey('7', $products);
		$this->assertArrayHasKey('8', $products);
	
		$product = $products['1'];
		$this->assertInstanceOf('Acme\Entity\Product', $product);
	
		$this->assertInternalType('integer', $product->id);
		$this->assertEquals(1, $product->id);
	
		$this->assertInternalType('string', $product->code);
		$this->assertEquals('IND00054', $product->code);
	
		$this->assertInternalType('string', $product->getCategory());
		$this->assertEquals('Clothes', $product->getCategory());
	
		$this->assertInstanceOf('Acme\RGBColor', $product->color);
	}
	
	public function testOverrideIndexList() {
		$products = $this->mapper->type('obj:Acme\Entity\Product[category]')
		->query("SELECT * FROM products ORDER BY product_id ASC");
	
		$this->assertInternalType('array', $products);
		$this->assertCount(5, $products);
	
		$this->assertArrayHasKey('Clothes', $products);
		$this->assertArrayHasKey('Hardware', $products);
		$this->assertArrayHasKey('Smartphones', $products);
		$this->assertArrayHasKey('Laptops', $products);
		$this->assertArrayHasKey('Software', $products);
	
		////
		$product = $products['Clothes'];
		$this->assertInstanceOf('Acme\Entity\Product', $product);
	
		$this->assertInternalType('integer', $product->id);
		$this->assertEquals(3, $product->id);
	
		$this->assertInternalType('string', $product->code);
		$this->assertEquals('IND00232', $product->code);
	
		$this->assertInternalType('string', $product->getCategory());
		$this->assertEquals('Clothes', $product->getCategory());
	
		$this->assertInstanceOf('Acme\RGBColor', $product->color);
	
		////
		$product = $products['Hardware'];
		$this->assertInstanceOf('Acme\Entity\Product', $product);
	
		$this->assertInternalType('integer', $product->id);
		$this->assertEquals(4, $product->id);
	
		$this->assertInternalType('string', $product->code);
		$this->assertEquals('GFX00067', $product->code);
	
		$this->assertInternalType('string', $product->getCategory());
		$this->assertEquals('Hardware', $product->getCategory());
	
		$this->assertNull($product->color);
	
		////
		$product = $products['Smartphones'];
		$this->assertInstanceOf('Acme\Entity\Product', $product);
	
		$this->assertInternalType('integer', $product->id);
		$this->assertEquals(7, $product->id);
	
		$this->assertInternalType('string', $product->code);
		$this->assertEquals('PHN00666', $product->code);
	
		$this->assertInternalType('string', $product->getCategory());
		$this->assertEquals('Smartphones', $product->getCategory());
	
		$this->assertInstanceOf('Acme\RGBColor', $product->color);
		
		////
		$product = $products['Laptops'];
		$this->assertInstanceOf('Acme\Entity\Product', $product);
		
		$this->assertInternalType('integer', $product->id);
		$this->assertEquals(6, $product->id);
		
		$this->assertInternalType('string', $product->code);
		$this->assertEquals('TEC00103', $product->code);
		
		$this->assertInternalType('string', $product->getCategory());
		$this->assertEquals('Laptops', $product->getCategory());
		
		$this->assertNull($product->color);
		
		////
		$product = $products['Software'];
		$this->assertInstanceOf('Acme\Entity\Product', $product);
		
		$this->assertInternalType('integer', $product->id);
		$this->assertEquals(8, $product->id);
		
		$this->assertInternalType('string', $product->code);
		$this->assertEquals('SOFT0134', $product->code);
		
		$this->assertInternalType('string', $product->getCategory());
		$this->assertEquals('Software', $product->getCategory());
		
		$this->assertNull($product->color);
	}
	
	public function testGroupedList() {
		$products = $this->mapper->type('obj:Acme\Entity\Product<category>')
		->query("SELECT * FROM products ORDER BY product_id ASC");
	
		$this->assertInternalType('array', $products);
		$this->assertCount(5, $products);
	
		$this->assertArrayHasKey('Clothes', $products);
		$this->assertArrayHasKey('Hardware', $products);
		$this->assertArrayHasKey('Smartphones', $products);
		$this->assertArrayHasKey('Laptops', $products);
		$this->assertArrayHasKey('Software', $products);
	
		$this->assertInternalType('array', $products['Clothes']);
		$this->assertCount(3, $products['Clothes']);
		$this->assertArrayHasKey(0, $products['Clothes']);
		$this->assertArrayHasKey(1, $products['Clothes']);
		$this->assertArrayHasKey(2, $products['Clothes']);
	
		$this->assertInternalType('array', $products['Hardware']);
		$this->assertCount(1, $products['Hardware']);
		$this->assertArrayHasKey(0, $products['Hardware']);
	
		$this->assertInternalType('array', $products['Smartphones']);
		$this->assertCount(2, $products['Smartphones']);
		$this->assertArrayHasKey(0, $products['Smartphones']);
		$this->assertArrayHasKey(1, $products['Smartphones']);
		
		$this->assertInternalType('array', $products['Laptops']);
		$this->assertCount(1, $products['Laptops']);
		$this->assertArrayHasKey(0, $products['Laptops']);
	
		////
		$product = $products['Clothes'][0];
		$this->assertInstanceOf('Acme\Entity\Product', $product);
	
		$this->assertInternalType('integer', $product->id);
		$this->assertEquals(1, $product->id);
	
		$this->assertInternalType('string', $product->code);
		$this->assertEquals('IND00054', $product->code);
	
		$this->assertInternalType('string', $product->getCategory());
		$this->assertEquals('Clothes', $product->getCategory());
	
		$this->assertInstanceOf('Acme\RGBColor', $product->color);
	
		////
		$product = $products['Clothes'][1];
		$this->assertInstanceOf('Acme\Entity\Product', $product);
	
		$this->assertInternalType('integer', $product->id);
		$this->assertEquals(2, $product->id);
	
		$this->assertInternalType('string', $product->code);
		$this->assertEquals('IND00043', $product->code);
	
		$this->assertInternalType('string', $product->getCategory());
		$this->assertEquals('Clothes', $product->getCategory());
	
		$this->assertInstanceOf('Acme\RGBColor', $product->color);
	
		////
		$product = $products['Clothes'][2];
		$this->assertInstanceOf('Acme\Entity\Product', $product);
	
		$this->assertInternalType('integer', $product->id);
		$this->assertEquals(3, $product->id);
	
		$this->assertInternalType('string', $product->code);
		$this->assertEquals('IND00232', $product->code);
	
		$this->assertInternalType('string', $product->getCategory());
		$this->assertEquals('Clothes', $product->getCategory());
	
		$this->assertInstanceOf('Acme\RGBColor', $product->color);
	
		////
		$product = $products['Hardware'][0];
		$this->assertInstanceOf('Acme\Entity\Product', $product);
	
		$this->assertInternalType('integer', $product->id);
		$this->assertEquals(4, $product->id);
	
		$this->assertInternalType('string', $product->code);
		$this->assertEquals('GFX00067', $product->code);
	
		$this->assertInternalType('string', $product->getCategory());
		$this->assertEquals('Hardware', $product->getCategory());
	
		$this->assertNull($product->color);
	
		////
		$product = $products['Smartphones'][0];
		$this->assertInstanceOf('Acme\Entity\Product', $product);
	
		$this->assertInternalType('integer', $product->id);
		$this->assertEquals(5, $product->id);
	
		$this->assertInternalType('string', $product->code);
		$this->assertEquals('PHN00098', $product->code);
	
		$this->assertInternalType('string', $product->getCategory());
		$this->assertEquals('Smartphones', $product->getCategory());
	
		$this->assertInstanceOf('Acme\RGBColor', $product->color);
		
		////
		$product = $products['Smartphones'][1];
		$this->assertInstanceOf('Acme\Entity\Product', $product);
		
		$this->assertInternalType('integer', $product->id);
		$this->assertEquals(7, $product->id);
		
		$this->assertInternalType('string', $product->code);
		$this->assertEquals('PHN00666', $product->code);
		
		$this->assertInternalType('string', $product->getCategory());
		$this->assertEquals('Smartphones', $product->getCategory());
		
		$this->assertInstanceOf('Acme\RGBColor', $product->color);
		
		////
		$product = $products['Laptops'][0];
		$this->assertInstanceOf('Acme\Entity\Product', $product);
		
		$this->assertInternalType('integer', $product->id);
		$this->assertEquals(6, $product->id);
		
		$this->assertInternalType('string', $product->code);
		$this->assertEquals('TEC00103', $product->code);
		
		$this->assertInternalType('string', $product->getCategory());
		$this->assertEquals('Laptops', $product->getCategory());
		
		$this->assertNull($product->color);
		
		////
		$product = $products['Software'][0];
		$this->assertInstanceOf('Acme\Entity\Product', $product);
		
		$this->assertInternalType('integer', $product->id);
		$this->assertEquals(8, $product->id);
		
		$this->assertInternalType('string', $product->code);
		$this->assertEquals('SOFT0134', $product->code);
		
		$this->assertInternalType('string', $product->getCategory());
		$this->assertEquals('Software', $product->getCategory());
		
		$this->assertNull($product->color);
	}
	
	public function testGroupedIndexedList() {
		$products = $this->mapper->type('obj:Acme\Entity\Product<category>[id]')
		->query("SELECT * FROM products ORDER BY product_id ASC");
	
		$this->assertInternalType('array', $products);
		$this->assertCount(5, $products);
	
		$this->assertArrayHasKey('Clothes', $products);
		$this->assertArrayHasKey('Hardware', $products);
		$this->assertArrayHasKey('Smartphones', $products);
		$this->assertArrayHasKey('Laptops', $products);
		$this->assertArrayHasKey('Software', $products);
	
		$this->assertInternalType('array', $products['Clothes']);
		$this->assertCount(3, $products['Clothes']);
		$this->assertArrayHasKey(1, $products['Clothes']);
		$this->assertArrayHasKey(2, $products['Clothes']);
		$this->assertArrayHasKey(3, $products['Clothes']);
	
		$this->assertInternalType('array', $products['Hardware']);
		$this->assertCount(1, $products['Hardware']);
		$this->assertArrayHasKey(4, $products['Hardware']);
	
		$this->assertInternalType('array', $products['Smartphones']);
		$this->assertCount(2, $products['Smartphones']);
		$this->assertArrayHasKey(5, $products['Smartphones']);
		$this->assertArrayHasKey(7, $products['Smartphones']);
	
		$this->assertInternalType('array', $products['Laptops']);
		$this->assertCount(1, $products['Laptops']);
		$this->assertArrayHasKey(6, $products['Laptops']);
		
		$this->assertInternalType('array', $products['Software']);
		$this->assertCount(1, $products['Software']);
		$this->assertArrayHasKey(8, $products['Software']);
		
		////
		$product = $products['Clothes'][1];
		$this->assertInstanceOf('Acme\Entity\Product', $product);
	
		$this->assertInternalType('integer', $product->id);
		$this->assertEquals(1, $product->id);
	
		$this->assertInternalType('string', $product->code);
		$this->assertEquals('IND00054', $product->code);
	
		$this->assertInternalType('string', $product->getCategory());
		$this->assertEquals('Clothes', $product->getCategory());
	
		$this->assertInstanceOf('Acme\RGBColor', $product->color);
	
		////
		$product = $products['Clothes'][2];
		$this->assertInstanceOf('Acme\Entity\Product', $product);
	
		$this->assertInternalType('integer', $product->id);
		$this->assertEquals(2, $product->id);
	
		$this->assertInternalType('string', $product->code);
		$this->assertEquals('IND00043', $product->code);
	
		$this->assertInternalType('string', $product->getCategory());
		$this->assertEquals('Clothes', $product->getCategory());
	
		$this->assertInstanceOf('Acme\RGBColor', $product->color);
	
		////
		$product = $products['Clothes'][3];
		$this->assertInstanceOf('Acme\Entity\Product', $product);
	
		$this->assertInternalType('integer', $product->id);
		$this->assertEquals(3, $product->id);
	
		$this->assertInternalType('string', $product->code);
		$this->assertEquals('IND00232', $product->code);
	
		$this->assertInternalType('string', $product->getCategory());
		$this->assertEquals('Clothes', $product->getCategory());
	
		$this->assertInstanceOf('Acme\RGBColor', $product->color);
	
		////
		$product = $products['Hardware'][4];
		$this->assertInstanceOf('Acme\Entity\Product', $product);
	
		$this->assertInternalType('integer', $product->id);
		$this->assertEquals(4, $product->id);
	
		$this->assertInternalType('string', $product->code);
		$this->assertEquals('GFX00067', $product->code);
	
		$this->assertInternalType('string', $product->getCategory());
		$this->assertEquals('Hardware', $product->getCategory());
	
		$this->assertNull($product->color);
	
		////
		$product = $products['Smartphones'][5];
		$this->assertInstanceOf('Acme\Entity\Product', $product);
	
		$this->assertInternalType('integer', $product->id);
		$this->assertEquals(5, $product->id);
	
		$this->assertInternalType('string', $product->code);
		$this->assertEquals('PHN00098', $product->code);
	
		$this->assertInternalType('string', $product->getCategory());
		$this->assertEquals('Smartphones', $product->getCategory());
	
		$this->assertInstanceOf('Acme\RGBColor', $product->color);
		
		////
		$product = $products['Smartphones'][7];
		$this->assertInstanceOf('Acme\Entity\Product', $product);
		
		$this->assertInternalType('integer', $product->id);
		$this->assertEquals(7, $product->id);
		
		$this->assertInternalType('string', $product->code);
		$this->assertEquals('PHN00666', $product->code);
		
		$this->assertInternalType('string', $product->getCategory());
		$this->assertEquals('Smartphones', $product->getCategory());
		
		$this->assertInstanceOf('Acme\RGBColor', $product->color);
		
		////
		$product = $products['Laptops'][6];
		$this->assertInstanceOf('Acme\Entity\Product', $product);
		
		$this->assertInternalType('integer', $product->id);
		$this->assertEquals(6, $product->id);
		
		$this->assertInternalType('string', $product->code);
		$this->assertEquals('TEC00103', $product->code);
		
		$this->assertInternalType('string', $product->getCategory());
		$this->assertEquals('Laptops', $product->getCategory());
		
		$this->assertNull($product->color);
		
		////
		$product = $products['Software'][8];
		$this->assertInstanceOf('Acme\Entity\Product', $product);
		
		$this->assertInternalType('integer', $product->id);
		$this->assertEquals(8, $product->id);
		
		$this->assertInternalType('string', $product->code);
		$this->assertEquals('SOFT0134', $product->code);
		
		$this->assertInternalType('string', $product->getCategory());
		$this->assertEquals('Software', $product->getCategory());
		
		$this->assertNull($product->color);
	}
}
?>