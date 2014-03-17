<?php
namespace eMapper\SQLite\Result\ObjectMapper;

use eMapper\SQLite\SQLiteTest;
use eMapper\Engine\SQLite\Result\SQLiteResultInterface;
use eMapper\Type\TypeManager;
use Acme\Type\RGBColorTypeHandler;
use eMapper\Result\Mapper\ObjectTypeMapper;

/**
 * Tests ObjectTypeMapper mapping to entity classes
 * @author emaphp
 * @group sqlite
 * @group result
 */
class EntityTest extends SQLiteTest {
	public $typeManager;
	
	public function __construct() {
		parent::__construct();
	
		$this->typeManager = new TypeManager();
		$this->typeManager->setTypeHandler('Acme\RGBColor', new RGBColorTypeHandler());
	}
	
	public function testRow() {
		$mapper = new ObjectTypeMapper($this->typeManager, 'Acme\Entity\Product', 'Acme\Entity\Product');
		$result = self::$conn->query("SELECT * FROM products WHERE product_id = 1");
		$product = $mapper->mapResult(new SQLiteResultInterface($result));
	
		$this->assertInstanceOf('Acme\Entity\Product', $product);
	
		$this->assertInternalType('integer', $product->id);
		$this->assertEquals(1, $product->id);
	
		$this->assertInternalType('string', $product->code);
		$this->assertEquals('IND00054', $product->code);
	
		$this->assertInternalType('string', $product->getCategory());
		$this->assertEquals('Clothes', $product->getCategory());
	
		$this->assertInstanceOf('Acme\RGBColor', $product->color);
	
		$result->finalize();
	}
	
	public function testList() {
		$mapper = new ObjectTypeMapper($this->typeManager, 'Acme\Entity\Product', 'Acme\Entity\Product');
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
		$this->assertInstanceOf('Acme\Entity\Product', $product);
	
		$this->assertInternalType('integer', $product->id);
		$this->assertEquals(1, $product->id);
	
		$this->assertInternalType('string', $product->code);
		$this->assertEquals('IND00054', $product->code);
	
		$this->assertInternalType('string', $product->getCategory());
		$this->assertEquals('Clothes', $product->getCategory());
	
		$this->assertInstanceOf('Acme\RGBColor', $product->color);
	
		$result->finalize();
	}
	
	public function testIndexedList() {
		$mapper = new ObjectTypeMapper($this->typeManager, 'Acme\Entity\Product', 'Acme\Entity\Product');
		$result = self::$conn->query("SELECT * FROM products ORDER BY product_id ASC");
		$products = $mapper->mapList(new SQLiteResultInterface($result), 'id');
	
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
	
		$result->finalize();
	}
	
	public function testCustomIndexList() {
		$mapper = new ObjectTypeMapper($this->typeManager, 'Acme\Entity\Product', 'Acme\Entity\Product');
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
		$this->assertInstanceOf('Acme\Entity\Product', $product);
	
		$this->assertInternalType('integer', $product->id);
		$this->assertEquals(1, $product->id);
	
		$this->assertInternalType('string', $product->code);
		$this->assertEquals('IND00054', $product->code);
	
		$this->assertInternalType('string', $product->getCategory());
		$this->assertEquals('Clothes', $product->getCategory());
	
		$this->assertInstanceOf('Acme\RGBColor', $product->color);
	
		$result->finalize();
	}
	
	public function testOverrideIndexList() {
		$mapper = new ObjectTypeMapper($this->typeManager, 'Acme\Entity\Product', 'Acme\Entity\Product');
		$result = self::$conn->query("SELECT * FROM products ORDER BY product_id ASC");
		$products = $mapper->mapList(new SQLiteResultInterface($result), 'category');
	
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
	
		$this->assertInstanceOf('Acme\RGBColor', $product->color);
	
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
	
		$result->finalize();
	}
	
	public function testGroupedList() {
		$mapper = new ObjectTypeMapper($this->typeManager, 'Acme\Entity\Product', 'Acme\Entity\Product');
		$result = self::$conn->query("SELECT * FROM products ORDER BY product_id ASC");
		$products = $mapper->mapList(new SQLiteResultInterface($result), null, null, 'category');
	
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
	
		$this->assertInstanceOf('Acme\RGBColor', $product->color);
	
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
	
		$result->finalize();
	}
	
	public function testGroupedIndexedList() {
		$mapper = new ObjectTypeMapper($this->typeManager, 'Acme\Entity\Product', 'Acme\Entity\Product');
		$result = self::$conn->query("SELECT * FROM products ORDER BY product_id ASC");
		$products = $mapper->mapList(new SQLiteResultInterface($result), 'id', null, 'category');
	
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
	
		$this->assertInstanceOf('Acme\RGBColor', $product->color);
	
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
	
		$result->finalize();
	}
}

?>