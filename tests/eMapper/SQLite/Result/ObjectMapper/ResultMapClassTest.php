<?php
namespace eMapper\SQLite\Result\ObjectMapper;

use eMapper\SQLite\SQLiteTest;
use eMapper\Engine\SQLite\Result\SQLiteResultInterface;
use eMapper\Type\TypeManager;
use Acme\Type\RGBColorTypeHandler;
use eMapper\Result\Mapper\ObjectTypeMapper;

/**
 * Test ObjectTypeMapper class with different results mapping to a custom class using a result map
 * @author emaphp
 * @group sqlite
 * @group result
 */
class ResultMapClassTest extends SQLiteTest {
	public function testRow() {
		$typeManager = new TypeManager();
		$typeManager->setTypeHandler('Acme\RGBColor', new RGBColorTypeHandler());
	
		$mapper = new ObjectTypeMapper($typeManager, 'Acme\Result\GenericProductResultMap', 'Acme\Generic\GenericProduct');
		$result = self::$conn->query("SELECT * FROM products WHERE product_id = 1");
		$product = $mapper->mapResult(new SQLiteResultInterface($result));
	
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
	
		$result->finalize();
	}
	
	public function testList() {
		$typeManager = new TypeManager();
		$typeManager->setTypeHandler('Acme\RGBColor', new RGBColorTypeHandler());
	
		$mapper = new ObjectTypeMapper($typeManager, 'Acme\Result\GenericProductResultMap', 'Acme\Generic\GenericProduct');
		$result = self::$conn->query("SELECT * FROM products ORDER BY product_id ASC");
		$products = $mapper->mapList(new SQLiteResultInterface($result));
	
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
	
		$result->finalize();
	}
	
	public function testIndexedList() {
		$typeManager = new TypeManager();
		$typeManager->setTypeHandler('Acme\RGBColor', new RGBColorTypeHandler());
	
		$mapper = new ObjectTypeMapper($typeManager, 'Acme\Result\GenericProductResultMap', 'Acme\Generic\GenericProduct');
		$result = self::$conn->query("SELECT * FROM products ORDER BY product_id ASC");
		$products = $mapper->mapList(new SQLiteResultInterface($result), 'code');
	
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
	
		$result->finalize();
	}
	
	public function testCustomIndexList() {
		$typeManager = new TypeManager();
		$typeManager->setTypeHandler('Acme\RGBColor', new RGBColorTypeHandler());
	
		$mapper = new ObjectTypeMapper($typeManager, 'Acme\Result\GenericProductResultMap', 'Acme\Generic\GenericProduct');
		$result = self::$conn->query("SELECT * FROM products ORDER BY product_id ASC");
		$products = $mapper->mapList(new SQLiteResultInterface($result), 'id', 'string');
	
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
	
		$result->finalize();
	}
	
	public function testOverrideIndexList() {
		$typeManager = new TypeManager();
		$typeManager->setTypeHandler('Acme\RGBColor', new RGBColorTypeHandler());
	
		$mapper = new ObjectTypeMapper($typeManager, 'Acme\Result\GenericProductResultMap', 'Acme\Generic\GenericProduct');
		$result = self::$conn->query("SELECT * FROM products ORDER BY product_id ASC");
		$products = $mapper->mapList(new SQLiteResultInterface($result), 'category');
	
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
	
		$result->finalize();
	}
	
	public function testGroupedList() {
		$typeManager = new TypeManager();
		$typeManager->setTypeHandler('Acme\RGBColor', new RGBColorTypeHandler());
	
		$mapper = new ObjectTypeMapper($typeManager, 'Acme\Result\GenericProductResultMap', 'Acme\Generic\GenericProduct');
		$result = self::$conn->query("SELECT * FROM products ORDER BY product_id ASC");
		$products = $mapper->mapList(new SQLiteResultInterface($result), null, null, 'category');
	
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
	
		$result->finalize();
	}
	
	public function testGroupedIndexedList() {
		$typeManager = new TypeManager();
		$typeManager->setTypeHandler('Acme\RGBColor', new RGBColorTypeHandler());
	
		$mapper = new ObjectTypeMapper($typeManager, 'Acme\Result\GenericProductResultMap', 'Acme\Generic\GenericProduct');
		$result = self::$conn->query("SELECT * FROM products ORDER BY product_id ASC");
		$products = $mapper->mapList(new SQLiteResultInterface($result), 'code', null, 'category');
	
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
	
		$result->finalize();
	}
}

?>