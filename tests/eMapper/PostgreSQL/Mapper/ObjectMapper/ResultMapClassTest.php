<?php
namespace eMapper\PostgreSQL\Mapper\ObjectMapper;

use eMapper\PostgreSQL\PostgreSQLTest;

/**
 * Tests Mapper mapping to objects using result maps
 * @author emaphp
 * @group postgre
 * @group mapper
 */
class ResultMapClassTest extends PostgreSQLTest {
	public function testRow() {
		$product = self::$mapper->type('obj:Acme\Generic\GenericProduct')
		->result_map('Acme\Result\GenericProductResultMap')
		->query("SELECT * FROM products WHERE product_id = 1");
	
		$this->assertInstanceOf('Acme\Generic\GenericProduct', $product);
	
		$this->assertInternalType('string', $product->getDescription());
		$this->assertEquals('Red dress', $product->getDescription());
	
		$this->assertInternalType('string', $product->getCode());
		$this->assertEquals('IND00054', $product->getCode());
	
		$this->assertInternalType('float', $product->getPrice());
		$this->assertEquals(150.65, $product->getPrice());
	
		$this->assertInternalType('string', $product->getCategory());
		$this->assertEquals('Clothes', $product->getCategory());
	
		$this->assertInstanceOf('Acme\RGBColor', $product->getColor());
	}
	
	public function testList() {
		$products = self::$mapper->type('obj:Acme\Generic\GenericProduct[]')
		->result_map('Acme\Result\GenericProductResultMap')
		->query("SELECT * FROM products ORDER BY product_id ASC");
	
		$this->assertInternalType('array', $products);
		$this->assertCount(5, $products);
		$this->assertArrayHasKey(0, $products);
		$this->assertArrayHasKey(1, $products);
		$this->assertArrayHasKey(2, $products);
		$this->assertArrayHasKey(3, $products);
		$this->assertArrayHasKey(4, $products);
	
		$product = $products[0];
		$this->assertInstanceOf('Acme\Generic\GenericProduct', $product);
	
		$this->assertInternalType('string', $product->getDescription());
		$this->assertEquals('Red dress', $product->getDescription());
	
		$this->assertInternalType('string', $product->getCode());
		$this->assertEquals('IND00054', $product->getCode());
	
		$this->assertInternalType('float', $product->getPrice());
		$this->assertEquals(150.65, $product->getPrice());
	
		$this->assertInternalType('string', $product->getCategory());
		$this->assertEquals('Clothes', $product->getCategory());
	
		$this->assertInstanceOf('Acme\RGBColor', $product->getColor());
	}
	
	public function testIndexedList() {
		$products = self::$mapper->type('obj:Acme\Generic\GenericProduct[code]')
		->result_map('Acme\Result\GenericProductResultMap')
		->query("SELECT * FROM products ORDER BY product_id ASC");
	
		$this->assertInternalType('array', $products);
		$this->assertCount(5, $products);
		$this->assertArrayHasKey('IND00054', $products);
		$this->assertArrayHasKey('IND00043', $products);
		$this->assertArrayHasKey('IND00232', $products);
		$this->assertArrayHasKey('GFX00067', $products);
		$this->assertArrayHasKey('PHN00098', $products);
	
		$product = $products['IND00054'];
		$this->assertInstanceOf('Acme\Generic\GenericProduct', $product);
	
		$this->assertInternalType('string', $product->getDescription());
		$this->assertEquals('Red dress', $product->getDescription());
	
		$this->assertInternalType('string', $product->getCode());
		$this->assertEquals('IND00054', $product->getCode());
	
		$this->assertInternalType('float', $product->getPrice());
		$this->assertEquals(150.65, $product->getPrice());
	
		$this->assertInternalType('string', $product->getCategory());
		$this->assertEquals('Clothes', $product->getCategory());
	
		$this->assertInstanceOf('Acme\RGBColor', $product->getColor());
	}
	
	public function testCustomIndexList() {
		$products = self::$mapper->type('obj:Acme\Generic\GenericProduct[id:string]')
		->result_map('Acme\Result\GenericProductResultMap')
		->query("SELECT * FROM products ORDER BY product_id ASC");
	
		$this->assertInternalType('array', $products);
		$this->assertCount(5, $products);
		$this->assertArrayHasKey('1', $products);
		$this->assertArrayHasKey('2', $products);
		$this->assertArrayHasKey('3', $products);
		$this->assertArrayHasKey('4', $products);
		$this->assertArrayHasKey('5', $products);
	
		$product = $products['1'];
		$this->assertInstanceOf('Acme\Generic\GenericProduct', $product);
	
		$this->assertInternalType('integer', $product->getId());
		$this->assertEquals(1, $product->getId());
	
		$this->assertInternalType('string', $product->getDescription());
		$this->assertEquals('Red dress', $product->getDescription());
	
		$this->assertInternalType('string', $product->getCode());
		$this->assertEquals('IND00054', $product->getCode());
	
		$this->assertInternalType('float', $product->getPrice());
		$this->assertEquals(150.65, $product->getPrice());
	
		$this->assertInternalType('string', $product->getCategory());
		$this->assertEquals('Clothes', $product->getCategory());
	
		$this->assertInstanceOf('Acme\RGBColor', $product->getColor());
	}
	
	public function testOverrideIndexList() {
		$products = self::$mapper->type('obj:Acme\Generic\GenericProduct[category]')
		->result_map('Acme\Result\GenericProductResultMap')
		->query("SELECT * FROM products ORDER BY product_id ASC");
	
		$this->assertInternalType('array', $products);
		$this->assertCount(3, $products);
	
		$this->assertArrayHasKey('Clothes', $products);
		$this->assertArrayHasKey('Hardware', $products);
		$this->assertArrayHasKey('Smartphones', $products);
	
		////
		$product = $products['Clothes'];
		$this->assertInstanceOf('Acme\Generic\GenericProduct', $product);
		$this->assertEquals(3, $product->getId());
	
		////
		$product = $products['Hardware'];
		$this->assertInstanceOf('Acme\Generic\GenericProduct', $product);
		$this->assertEquals(4, $product->getId());
	
		////
		$product = $products['Smartphones'];
		$this->assertInstanceOf('Acme\Generic\GenericProduct', $product);
		$this->assertEquals(5, $product->getId());
	}
	
	public function testGroupedList() {
		$products = self::$mapper->type('obj:Acme\Generic\GenericProduct<category>')
		->result_map('Acme\Result\GenericProductResultMap')
		->query("SELECT * FROM products ORDER BY product_id ASC");
	
		$this->assertInternalType('array', $products);
		$this->assertCount(3, $products);
	
		$this->assertArrayHasKey('Clothes', $products);
		$this->assertArrayHasKey('Hardware', $products);
		$this->assertArrayHasKey('Smartphones', $products);
	
		$this->assertInternalType('array', $products['Clothes']);
		$this->assertCount(3, $products['Clothes']);
		$this->assertInternalType('array', $products['Hardware']);
		$this->assertCount(1, $products['Hardware']);
		$this->assertInternalType('array', $products['Smartphones']);
		$this->assertCount(1, $products['Smartphones']);
	
		$this->assertArrayHasKey(0, $products['Clothes']);
		$this->assertArrayHasKey(1, $products['Clothes']);
		$this->assertArrayHasKey(2, $products['Clothes']);
		$this->assertArrayHasKey(0, $products['Hardware']);
		$this->assertArrayHasKey(0, $products['Smartphones']);
	
		///
		$product = $products['Clothes'][0];
		$this->assertInstanceOf('Acme\Generic\GenericProduct', $product);
		$this->assertEquals('IND00054', $product->getCode());
	
		///
		$product = $products['Clothes'][1];
		$this->assertInstanceOf('Acme\Generic\GenericProduct', $product);
		$this->assertEquals('IND00043', $product->getCode());
	
		///
		$product = $products['Clothes'][2];
		$this->assertInstanceOf('Acme\Generic\GenericProduct', $product);
		$this->assertEquals('IND00232', $product->getCode());
	
		///
		$product = $products['Hardware'][0];
		$this->assertInstanceOf('Acme\Generic\GenericProduct', $product);
		$this->assertEquals('GFX00067', $product->getCode());
	
		///
		$product = $products['Smartphones'][0];
		$this->assertInstanceOf('Acme\Generic\GenericProduct', $product);
		$this->assertEquals('PHN00098', $product->getCode());
	}
	
	public function testGroupedIndexedList() {
		$products = self::$mapper->type('obj:Acme\Generic\GenericProduct<category>[code]')
		->result_map('Acme\Result\GenericProductResultMap')
		->query("SELECT * FROM products ORDER BY product_id ASC");
	
		$this->assertInternalType('array', $products);
		$this->assertCount(3, $products);
	
		$this->assertArrayHasKey('Clothes', $products);
		$this->assertArrayHasKey('Hardware', $products);
		$this->assertArrayHasKey('Smartphones', $products);
	
		$this->assertInternalType('array', $products['Clothes']);
		$this->assertCount(3, $products['Clothes']);
		$this->assertInternalType('array', $products['Hardware']);
		$this->assertCount(1, $products['Hardware']);
		$this->assertInternalType('array', $products['Smartphones']);
		$this->assertCount(1, $products['Smartphones']);
	
		$this->assertArrayHasKey('IND00054', $products['Clothes']);
		$this->assertInstanceOf('Acme\Generic\GenericProduct', $products['Clothes']['IND00054']);
		$this->assertEquals('IND00054', $products['Clothes']['IND00054']->getCode());
		$this->assertArrayHasKey('IND00043', $products['Clothes']);
		$this->assertInstanceOf('Acme\Generic\GenericProduct', $products['Clothes']['IND00043']);
		$this->assertEquals('IND00043', $products['Clothes']['IND00043']->getCode());
		$this->assertArrayHasKey('IND00232', $products['Clothes']);
		$this->assertInstanceOf('Acme\Generic\GenericProduct', $products['Clothes']['IND00232']);
		$this->assertEquals('IND00232', $products['Clothes']['IND00232']->getCode());
		$this->assertArrayHasKey('GFX00067', $products['Hardware']);
		$this->assertInstanceOf('Acme\Generic\GenericProduct', $products['Hardware']['GFX00067']);
		$this->assertEquals('GFX00067', $products['Hardware']['GFX00067']->getCode());
		$this->assertArrayHasKey('PHN00098', $products['Smartphones']);
		$this->assertInstanceOf('Acme\Generic\GenericProduct', $products['Smartphones']['PHN00098']);
		$this->assertEquals('PHN00098', $products['Smartphones']['PHN00098']->getCode());
	
		$product = $products['Clothes']['IND00054'];
	
		$this->assertInternalType('string', $product->getDescription());
		$this->assertEquals('Red dress', $product->getDescription());
	
		$this->assertInternalType('string', $product->getCode());
		$this->assertEquals('IND00054', $product->getCode());
	
		$this->assertInternalType('float', $product->getPrice());
		$this->assertEquals(150.65, $product->getPrice());
	
		$this->assertInternalType('string', $product->getCategory());
		$this->assertEquals('Clothes', $product->getCategory());
	
		$this->assertInstanceOf('Acme\RGBColor', $product->getColor());
	}
}

?>