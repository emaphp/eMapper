<?php
namespace eMapper\PostgreSQL\Result\ObjectMapper;

use eMapper\PostgreSQL\PostgreSQLTest;
use eMapper\Engine\PostgreSQL\Type\PostgreSQLTypeManager;
use Acme\Type\RGBColorTypeHandler;
use eMapper\Engine\PostgreSQL\Result\PostgreSQLResultIterator;
use eMapper\Result\Mapper\EntityMapper;

/**
 * Test ObjectTypeMapper class mapping to entities
 *
 * @author emaphp
 * @group result
 * @group postgre
 */
class EntityTest extends PostgreSQLTest {
	public $typeManager;
	
	public function __construct() {
		$this->typeManager = new PostgreSQLTypeManager();
		$this->typeManager->setTypeHandler('Acme\RGBColor', new RGBColorTypeHandler());
	}
	
	public function testRow() {
		$mapper = new EntityMapper($this->typeManager, 'Acme\Entity\Product');
		$result = pg_query(self::$conn, "SELECT * FROM products WHERE product_id = 1");
		$product = $mapper->mapResult(new PostgreSQLResultIterator($result));
	
		$this->assertInstanceOf('Acme\Entity\Product', $product);
	
		$this->assertInternalType('integer', $product->id);
		$this->assertEquals(1, $product->id);
	
		$this->assertInternalType('string', $product->code);
		$this->assertEquals('IND00054', $product->code);
	
		$this->assertInternalType('string', $product->getCategory());
		$this->assertEquals('Clothes', $product->getCategory());
	
		$this->assertInstanceOf('Acme\RGBColor', $product->color);
	
		pg_free_result($result);
	}
	
	public function testList() {
		$mapper = new EntityMapper($this->typeManager, 'Acme\Entity\Product');
		$result = pg_query(self::$conn, "SELECT * FROM products ORDER BY product_id ASC");
		$products = $mapper->mapList(new PostgreSQLResultIterator($result));
	
		$this->assertInternalType('array', $products);
		$this->assertCount(5, $products);
	
		$this->assertArrayHasKey(0, $products);
		$this->assertArrayHasKey(1, $products);
		$this->assertArrayHasKey(2, $products);
		$this->assertArrayHasKey(3, $products);
		$this->assertArrayHasKey(4, $products);
	
		$product = $products[0];
		$this->assertInstanceOf('Acme\Entity\Product', $product);
	
		$this->assertInternalType('integer', $product->id);
		$this->assertEquals(1, $product->id);
	
		$this->assertInternalType('string', $product->code);
		$this->assertEquals('IND00054', $product->code);
	
		$this->assertInternalType('string', $product->getCategory());
		$this->assertEquals('Clothes', $product->getCategory());
	
		$this->assertInstanceOf('Acme\RGBColor', $product->color);
	
		pg_free_result($result);
	}
	
	public function testIndexedList() {
		$mapper = new EntityMapper($this->typeManager, 'Acme\Entity\Product');
		$result = pg_query(self::$conn, "SELECT * FROM products ORDER BY product_id ASC");
		$products = $mapper->mapList(new PostgreSQLResultIterator($result), 'id');
	
		$this->assertInternalType('array', $products);
		$this->assertCount(5, $products);
	
		$this->assertArrayHasKey(1, $products);
		$this->assertArrayHasKey(2, $products);
		$this->assertArrayHasKey(3, $products);
		$this->assertArrayHasKey(4, $products);
		$this->assertArrayHasKey(5, $products);
	
		$product = $products[1];
		$this->assertInstanceOf('Acme\Entity\Product', $product);
	
		$this->assertInternalType('integer', $product->id);
		$this->assertEquals(1, $product->id);
	
		$this->assertInternalType('string', $product->code);
		$this->assertEquals('IND00054', $product->code);
	
		$this->assertInternalType('string', $product->getCategory());
		$this->assertEquals('Clothes', $product->getCategory());
	
		$this->assertInstanceOf('Acme\RGBColor', $product->color);
	
		pg_free_result($result);
	}
	
	public function testCustomIndexList() {
		$mapper = new EntityMapper($this->typeManager, 'Acme\Entity\Product');
		$result = pg_query(self::$conn, "SELECT * FROM products ORDER BY product_id ASC");
		$products = $mapper->mapList(new PostgreSQLResultIterator($result), 'id', 'string');
	
		$this->assertInternalType('array', $products);
		$this->assertCount(5, $products);
	
		$this->assertArrayHasKey('1', $products);
		$this->assertArrayHasKey('2', $products);
		$this->assertArrayHasKey('3', $products);
		$this->assertArrayHasKey('4', $products);
		$this->assertArrayHasKey('5', $products);
	
		$product = $products['1'];
		$this->assertInstanceOf('Acme\Entity\Product', $product);
	
		$this->assertInternalType('integer', $product->id);
		$this->assertEquals(1, $product->id);
	
		$this->assertInternalType('string', $product->code);
		$this->assertEquals('IND00054', $product->code);
	
		$this->assertInternalType('string', $product->getCategory());
		$this->assertEquals('Clothes', $product->getCategory());
	
		$this->assertInstanceOf('Acme\RGBColor', $product->color);
	
		pg_free_result($result);
	}
	
	public function testOverrideIndexList() {
		$mapper = new EntityMapper($this->typeManager, 'Acme\Entity\Product');
		$result = pg_query(self::$conn, "SELECT * FROM products ORDER BY product_id ASC");
		$products = $mapper->mapList(new PostgreSQLResultIterator($result), 'category');
	
		$this->assertInternalType('array', $products);
		$this->assertCount(3, $products);
	
		$this->assertArrayHasKey('Clothes', $products);
		$this->assertArrayHasKey('Hardware', $products);
		$this->assertArrayHasKey('Smartphones', $products);
	
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
		$this->assertEquals(5, $product->id);
	
		$this->assertInternalType('string', $product->code);
		$this->assertEquals('PHN00098', $product->code);
	
		$this->assertInternalType('string', $product->getCategory());
		$this->assertEquals('Smartphones', $product->getCategory());
	
		$this->assertInstanceOf('Acme\RGBColor', $product->color);
	
		pg_free_result($result);
	}
	
	public function testGroupedList() {
		$mapper = new EntityMapper($this->typeManager, 'Acme\Entity\Product');
		$result = pg_query(self::$conn, "SELECT * FROM products ORDER BY product_id ASC");
		$products = $mapper->mapList(new PostgreSQLResultIterator($result), null, null, 'category');
	
		$this->assertInternalType('array', $products);
		$this->assertCount(3, $products);
	
		$this->assertArrayHasKey('Clothes', $products);
		$this->assertArrayHasKey('Hardware', $products);
		$this->assertArrayHasKey('Smartphones', $products);
	
		$this->assertInternalType('array', $products['Clothes']);
		$this->assertCount(3, $products['Clothes']);
		$this->assertArrayHasKey(0, $products['Clothes']);
		$this->assertArrayHasKey(1, $products['Clothes']);
		$this->assertArrayHasKey(2, $products['Clothes']);
	
		$this->assertInternalType('array', $products['Hardware']);
		$this->assertCount(1, $products['Hardware']);
		$this->assertArrayHasKey(0, $products['Hardware']);
	
		$this->assertInternalType('array', $products['Smartphones']);
		$this->assertCount(1, $products['Smartphones']);
		$this->assertArrayHasKey(0, $products['Smartphones']);
	
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
	
		pg_free_result($result);
	}
	
	public function testGroupedIndexedList() {
		$mapper = new EntityMapper($this->typeManager, 'Acme\Entity\Product');
		$result = pg_query(self::$conn, "SELECT * FROM products ORDER BY product_id ASC");
		$products = $mapper->mapList(new PostgreSQLResultIterator($result), 'id', null, 'category');
	
		$this->assertInternalType('array', $products);
		$this->assertCount(3, $products);
	
		$this->assertArrayHasKey('Clothes', $products);
		$this->assertArrayHasKey('Hardware', $products);
		$this->assertArrayHasKey('Smartphones', $products);
	
		$this->assertInternalType('array', $products['Clothes']);
		$this->assertCount(3, $products['Clothes']);
		$this->assertArrayHasKey(1, $products['Clothes']);
		$this->assertArrayHasKey(2, $products['Clothes']);
		$this->assertArrayHasKey(3, $products['Clothes']);
	
		$this->assertInternalType('array', $products['Hardware']);
		$this->assertCount(1, $products['Hardware']);
		$this->assertArrayHasKey(4, $products['Hardware']);
	
		$this->assertInternalType('array', $products['Smartphones']);
		$this->assertCount(1, $products['Smartphones']);
		$this->assertArrayHasKey(5, $products['Smartphones']);
	
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
	
		pg_free_result($result);
	}
}

?>