<?php
namespace eMapper;

use eMapper\Query\Attr;
use eMapper\Query\Column;
use eMapper\Query\Q;
use eMapper\Engine\Generic\Driver;

abstract class AbstractManagerTest extends \PHPUnit_Framework_TestCase {
	/**
	 * Engine driver
	 * @var Driver
	 */
	protected $driver;
	
	/**
	 * Mapper instance
	 * @var Mapper
	 */
	protected $mapper;
	
	/**
	 * Test manager
	 * @var Manager
	 */
	protected $productsManager;
	
	public function setUp() {
		$this->build();
	}
	
	public abstract function build();
	
	public function testFindByPk() {
		$product = $this->productsManager->findByPK(1);
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
		$product = $this->productsManager->findByPK(100);
		$this->assertNull($product);
	}
	
	public function testGet() {
		$product = $this->productsManager->get(Attr::code()->eq('IND00232'));
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
		$product = $this->productsManager->get(Attr::code()->eq('notfound'));
		$this->assertNull($product);
	}
	
	public function testFindAll() {
		$products = $this->productsManager->find();
		$this->assertInternalType('array', $products);
		$this->assertCount(5, $products);
	
		foreach ($products as $product) {
			$this->assertInstanceOf('Acme\Entity\Product', $product);
		}
	}
	
	public function testFindNone() {
		$products = $this->productsManager->find(Attr::id()->gt(10));
		$this->assertInternalType('array', $products);
		$this->assertCount(0, $products);
	}
	
	public function testCondition() {
		$products = $this->productsManager->find(Attr::category()->eq('Clothes'));
		$this->assertCount(3, $products);
	}
	
	public function testReverseCondition() {
		$products = $this->productsManager->find(Attr::category()->eq('Clothes', false));
		$this->assertCount(2, $products);
	}
	
	public function testFilter() {
		$products = $this->productsManager->filter(Attr::id()->lt(3))->find();
		$this->assertCount(2, $products);
	}
	
	public function testExclude() {
		$products = $this->productsManager->exclude(Attr::id()->lt(3))->find();
		$this->assertCount(3, $products);
	}
	
	/**
	 * Tests that the condition specified in finf overrides all others setted in filter/exclude
	 */
	public function testConditionOverride() {
		$products = $this->productsManager->filter(Attr::id()->lt(3))->find(Attr::category()->eq('Clothes'));
		$this->assertCount(3, $products);
	}
	
	public function testColumnCondition() {
		$products = $this->productsManager->find(Column::manufacture_year()->eq(2013));
		$this->assertCount(2, $products);
	}
	
	public function testColumnReverseCondition() {
		$products = $this->productsManager->find(Column::manufacture_year()->eq(2013, false));
		$this->assertCount(3, $products);
	}
	
	/**
	 * Tests setting an OR condition using the Q class
	 */
	public function testOrCondition() {
		$products = $this->productsManager->find(Q::where(Attr::id()->eq(5), Attr::id()->eq(3)));
		$this->assertCount(2, $products);
	}
}
?>