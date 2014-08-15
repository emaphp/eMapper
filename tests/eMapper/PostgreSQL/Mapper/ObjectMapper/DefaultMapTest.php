<?php
namespace eMapper\PostgreSQL\Mapper\ObjectMapper;

use eMapper\PostgreSQL\PostgreSQLConfig;
use eMapper\Mapper\ObjectMapper\AbstractDefaultMapTest;

/**
 * Tests Mapper class obtaining default stdClass instances
 * @author emaphp
 * @group postgre
 * @group mapper
 */
class DefaultMapTest extends AbstractDefaultMapTest {
	use PostgreSQLConfig;
	
	public function testOverrideIndexList() {
		$products = $this->mapper->type('object[category]')->query("SELECT * FROM products ORDER BY product_id ASC");
	
		$this->assertInternalType('array', $products);
		$this->assertCount(3, $products);
		$this->assertArrayHasKey('Clothes', $products);
		$this->assertArrayHasKey('Hardware', $products);
		$this->assertArrayHasKey('Smartphones', $products);
	
		////
		$product = $products['Clothes'];
		$this->assertInstanceOf('stdClass', $product);
	
		$this->assertObjectHasAttribute('product_id', $product);
		$this->assertInternalType('integer', $product->product_id);
		$this->assertEquals(3, $product->product_id);
	
		$this->assertObjectHasAttribute('product_code', $product);
		$this->assertInternalType('string', $product->product_code);
		$this->assertEquals('IND00232', $product->product_code);
	
		$this->assertObjectHasAttribute('description', $product);
		$this->assertInternalType('string', $product->description);
		$this->assertEquals('Green shirt', $product->description);
	
		$this->assertObjectHasAttribute('color', $product);
		$this->assertInternalType('string', $product->color);
		$this->assertEquals('707c04', $product->color);
	
		$this->assertObjectHasAttribute('price', $product);
		$this->assertInternalType('float', $product->price);
		$this->assertEquals(70.9, $product->price);
	
		$this->assertObjectHasAttribute('category', $product);
		$this->assertInternalType('string', $product->category);
		$this->assertEquals('Clothes', $product->category);
	
		$this->assertObjectHasAttribute('rating', $product);
		$this->assertInternalType('float', $product->rating);
		$this->assertEquals(4.1, $product->rating);
	
		$this->assertObjectHasAttribute('refurbished', $product);
		$this->assertInternalType('boolean', $product->refurbished);
		$this->assertFalse($product->refurbished);
	
		$this->assertObjectHasAttribute('manufacture_year', $product);
		$this->assertInternalType('integer', $product->manufacture_year);
		$this->assertEquals(2013, $product->manufacture_year);
	
		////
		$product = $products['Hardware'];
		$this->assertInstanceOf('stdClass', $product);
		$this->assertEquals(4, $product->product_id);
	
		////
		$product = $products['Smartphones'];
		$this->assertInstanceOf('stdClass', $product);
		$this->assertEquals(5, $product->product_id);
	}
	
	public function testGroupedList() {
		$products = $this->mapper->type('object<category>')->query("SELECT * FROM products ORDER BY product_id ASC");
	
		$this->assertInternalType('array', $products);
		$this->assertCount(3, $products);
	
		$this->assertArrayHasKey('Clothes', $products);
		$this->assertInternalType('array', $products['Clothes']);
		$this->assertCount(3, $products['Clothes']);
		$this->assertArrayHasKey(0, $products['Clothes']);
		$this->assertArrayHasKey(1, $products['Clothes']);
		$this->assertArrayHasKey(2, $products['Clothes']);
	
		//
		$product = $products['Clothes'][0];
		$this->assertInstanceOf('stdClass', $product);
	
		$this->assertObjectHasAttribute('product_id', $product);
		$this->assertInternalType('integer', $product->product_id);
		$this->assertEquals(1, $product->product_id);
	
		$this->assertObjectHasAttribute('product_code', $product);
		$this->assertInternalType('string', $product->product_code);
		$this->assertEquals('IND00054', $product->product_code);
	
		$this->assertObjectHasAttribute('description', $product);
		$this->assertInternalType('string', $product->description);
		$this->assertEquals('Red dress', $product->description);
	
		$this->assertObjectHasAttribute('color', $product);
		$this->assertInternalType('string', $product->color);
		$this->assertEquals('e11a1a', $product->color);

		$this->assertObjectHasAttribute('price', $product);
		$this->assertInternalType('float', $product->price);
		$this->assertEquals(150.65, $product->price);

		$this->assertObjectHasAttribute('category', $product);
		$this->assertInternalType('string', $product->category);
		$this->assertEquals('Clothes', $product->category);
	
		$this->assertObjectHasAttribute('rating', $product);
		$this->assertInternalType('float', $product->rating);
		$this->assertEquals(4.5, $product->rating);

		$this->assertObjectHasAttribute('refurbished', $product);
		$this->assertInternalType('boolean', $product->refurbished);
		$this->assertFalse($product->refurbished);

		$this->assertObjectHasAttribute('manufacture_year', $product);
		$this->assertInternalType('integer', $product->manufacture_year);
		$this->assertEquals(2011, $product->manufacture_year);

		////
		$this->assertArrayHasKey('Hardware', $products);
		$this->assertInternalType('array', $products);
		$this->assertCount(1, $products['Hardware']);
		$this->assertArrayHasKey(0, $products['Hardware']);

		$product = $products['Hardware'][0];
		$this->assertInstanceOf('stdClass', $product);

		$this->assertObjectHasAttribute('product_id', $product);
		$this->assertInternalType('integer', $product->product_id);
		$this->assertEquals(4, $product->product_id);

		////
		$this->assertArrayHasKey('Smartphones', $products);
		$this->assertInternalType('array', $products);
		$this->assertCount(1, $products['Smartphones']);
		$this->assertArrayHasKey(0, $products['Smartphones']);

		$product = $products['Smartphones'][0];
		$this->assertInstanceOf('stdClass', $product);

		$this->assertObjectHasAttribute('product_id', $product);
		$this->assertInternalType('integer', $product->product_id);
		$this->assertEquals(5, $product->product_id);
	}
	
	public function testGroupedIndexedList() {
		$products = $this->mapper->type('object<category>[product_id]')->query("SELECT * FROM products ORDER BY product_id ASC");
	
		$this->assertInternalType('array', $products);
		$this->assertCount(3, $products);
	
		$this->assertArrayHasKey('Clothes', $products);
		$this->assertInternalType('array', $products['Clothes']);
		$this->assertCount(3, $products['Clothes']);
		$this->assertArrayHasKey(1, $products['Clothes']);
		$this->assertArrayHasKey(2, $products['Clothes']);
		$this->assertArrayHasKey(3, $products['Clothes']);
	
		//
		$product = $products['Clothes'][1];
		$this->assertInstanceOf('stdClass', $product);
	
		$this->assertObjectHasAttribute('product_id', $product);
		$this->assertInternalType('integer', $product->product_id);
		$this->assertEquals(1, $product->product_id);
	
		$this->assertObjectHasAttribute('product_code', $product);
		$this->assertInternalType('string', $product->product_code);
		$this->assertEquals('IND00054', $product->product_code);
	
		$this->assertObjectHasAttribute('description', $product);
		$this->assertInternalType('string', $product->description);
		$this->assertEquals('Red dress', $product->description);

		$this->assertObjectHasAttribute('color', $product);
		$this->assertInternalType('string', $product->color);
		$this->assertEquals('e11a1a', $product->color);

		$this->assertObjectHasAttribute('price', $product);
		$this->assertInternalType('float', $product->price);
		$this->assertEquals(150.65, $product->price);

		$this->assertObjectHasAttribute('category', $product);
		$this->assertInternalType('string', $product->category);
		$this->assertEquals('Clothes', $product->category);

		$this->assertObjectHasAttribute('rating', $product);
		$this->assertInternalType('float', $product->rating);
		$this->assertEquals(4.5, $product->rating);

		$this->assertObjectHasAttribute('refurbished', $product);
		$this->assertInternalType('boolean', $product->refurbished);
		$this->assertFalse($product->refurbished);

		$this->assertObjectHasAttribute('manufacture_year', $product);
		$this->assertInternalType('integer', $product->manufacture_year);
		$this->assertEquals(2011, $product->manufacture_year);

		////
		$this->assertArrayHasKey('Hardware', $products);
		$this->assertInternalType('array', $products);
		$this->assertCount(1, $products['Hardware']);
		$this->assertArrayHasKey(4, $products['Hardware']);

		$product = $products['Hardware'][4];
		$this->assertInstanceOf('stdClass', $product);

		$this->assertObjectHasAttribute('product_id', $product);
		$this->assertInternalType('integer', $product->product_id);
		$this->assertEquals(4, $product->product_id);

		////
		$this->assertArrayHasKey('Smartphones', $products);
		$this->assertInternalType('array', $products);
		$this->assertCount(1, $products['Smartphones']);
		$this->assertArrayHasKey(5, $products['Smartphones']);

		$product = $products['Smartphones'][5];
		$this->assertInstanceOf('stdClass', $product);

		$this->assertObjectHasAttribute('product_id', $product);
		$this->assertInternalType('integer', $product->product_id);
		$this->assertEquals(5, $product->product_id);
	}
}

?>