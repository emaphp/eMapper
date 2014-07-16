<?php
namespace eMapper\MySQL;

use eMapper\Engine\MySQL\MySQLDriver;
use eMapper\Mapper;
use Acme\Type\RGBColorTypeHandler;
use eMapper\Query\Attr;
use eMapper\Query\Column;
use eMapper\Query\Q;

/**
 * MySQL manager test
 * @author emaphp
 * @group mysql
 * @group manager
 */
class ManagerTest extends MySQLTest {
	protected $_driver;
	protected $_mapper;
	protected $_manager;
	
	public function setUp() {
		$this->_driver = new MySQLDriver(self::$config['database'], self::$config['host'], self::$config['user'], self::$config['password']);
		$this->_mapper = new Mapper($this->_driver);
		$this->_mapper->addType('Acme\RGBColor', new RGBColorTypeHandler());
		$this->_manager = $this->_mapper->buildManager('Acme\Entity\Product');
	}
	
	public function testFindByPk() {
		$product = $this->_manager->findByPK(1);
		$this->assertInstanceOf('Acme\Entity\Product', $product);
		$this->assertEquals(1, $product->id);
		$this->assertEquals('IND00054', $product->code);
		$this->assertEquals('Clothes', $product->getCategory());
		$this->assertInstanceOf('Acme\RGBColor', $product->color);
		$this->assertEquals(225, $product->color->red);
		$this->assertEquals(26, $product->color->green);
		$this->assertEquals(26, $product->color->blue);
	}
	
	public function testFindByMissingPk() {
		$product = $this->_manager->findByPK(100);
		$this->assertNull($product);
	}
	
	public function testGet() {
		$product = $this->_manager->get(Attr::code()->eq('IND00232'));
		$this->assertInstanceOf('Acme\Entity\Product', $product);
		$this->assertEquals(3, $product->id);
		$this->assertEquals('IND00232', $product->code);
		$this->assertEquals('Clothes', $product->getCategory());
		$this->assertInstanceOf('Acme\RGBColor', $product->color);
		$this->assertEquals(112, $product->color->red);
		$this->assertEquals(124, $product->color->green);
		$this->assertEquals(4, $product->color->blue);
	}
	
	public function testGetNull() {
		$product = $this->_manager->get(Attr::code()->eq('notfound'));
		$this->assertNull($product);
	}
	
	public function testFindAll() {
		$products = $this->_manager->find();
		$this->assertInternalType('array', $products);
		$this->assertCount(5, $products);
		
		foreach ($products as $product) {
			$this->assertInstanceOf('Acme\Entity\Product', $product);
		}
	}
	
	public function testFindNone() {
		$products = $this->_manager->find(Attr::id()->gt(10));
		$this->assertInternalType('array', $products);
		$this->assertCount(0, $products);
	}
	
	public function testCondition() {
		$products = $this->_manager->find(Attr::category()->eq('Clothes'));
		$this->assertCount(3, $products);
	}
	
	public function testReverseCondition() {
		$products = $this->_manager->find(Attr::category()->eq('Clothes', false));
		$this->assertCount(2, $products);
	}
	
	public function testFilter() {
		$products = $this->_manager->filter(Attr::id()->lt(3))->find();
		$this->assertCount(2, $products);
	}
	
	public function testExclude() {
		$products = $this->_manager->exclude(Attr::id()->lt(3))->find();
		$this->assertCount(3, $products);
	}
	
	/**
	 * Tests that the condition specified in finf overrides all others setted in filter/exclude
	 */
	public function testConditionOverride() {
		$products = $this->_manager->filter(Attr::id()->lt(3))->find(Attr::category()->eq('Clothes'));
		$this->assertCount(3, $products);
	}
	
	public function testColumnCondition() {
		$products = $this->_manager->find(Column::manufacture_year()->eq(2013));
		$this->assertCount(2, $products);
	}
	
	public function testColumnReverseCondition() {
		$products = $this->_manager->find(Column::manufacture_year()->eq(2013, false));
		$this->assertCount(3, $products);
	}
	
	/**
	 * Tests setting an OR condition using the Q class
	 */
	public function testOrCondition() {
		$products = $this->_manager->find(Q::where(Attr::id()->eq(5), Attr::id()->eq(3)));
		$this->assertCount(2, $products);
	}
}
?>