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
	
	/*
	 * INDEXATION
	 */
	
	public function testIndex() {
		$products = $this->productsManager->index(Attr::id())->find();
		$this->assertCount(5, $products);
		
		//assert indexes
		$this->assertArrayHasKey(1, $products);
		$this->assertArrayHasKey(2, $products);
		$this->assertArrayHasKey(3, $products);
		$this->assertArrayHasKey(4, $products);
		$this->assertArrayHasKey(5, $products);
		
		//assert objects
		$this->assertInstanceOf('Acme\Entity\Product', $products[1]);
		$this->assertEquals('IND00054', $products[1]->code);
		
		$this->assertInstanceOf('Acme\Entity\Product', $products[2]);
		$this->assertEquals('IND00043', $products[2]->code);
		
		$this->assertInstanceOf('Acme\Entity\Product', $products[3]);
		$this->assertEquals('IND00232', $products[3]->code);
		
		$this->assertInstanceOf('Acme\Entity\Product', $products[4]);
		$this->assertEquals('GFX00067', $products[4]->code);
		
		$this->assertInstanceOf('Acme\Entity\Product', $products[5]);
		$this->assertEquals('PHN00098', $products[5]->code);
	}
	
	public function testIndexType() {
		$products = $this->productsManager->index(Attr::id('string'))->find();
		$this->assertCount(5, $products);
	
		//assert indexes
		$this->assertArrayHasKey('1', $products);
		$this->assertArrayHasKey('2', $products);
		$this->assertArrayHasKey('3', $products);
		$this->assertArrayHasKey('4', $products);
		$this->assertArrayHasKey('5', $products);
	
		//assert objects
		$this->assertInstanceOf('Acme\Entity\Product', $products['1']);
		$this->assertEquals('IND00054', $products['1']->code);
	
		$this->assertInstanceOf('Acme\Entity\Product', $products['2']);
		$this->assertEquals('IND00043', $products['2']->code);
	
		$this->assertInstanceOf('Acme\Entity\Product', $products['3']);
		$this->assertEquals('IND00232', $products['3']->code);
	
		$this->assertInstanceOf('Acme\Entity\Product', $products['4']);
		$this->assertEquals('GFX00067', $products['4']->code);
	
		$this->assertInstanceOf('Acme\Entity\Product', $products['5']);
		$this->assertEquals('PHN00098', $products['5']->code);
	}
	
	public function testIndexColumn() {
		$products = $this->productsManager->index(Column::product_code())->find();
		$this->assertCount(5, $products);
		
		//assert indexes
		$this->assertArrayHasKey('IND00054', $products);
		$this->assertArrayHasKey('IND00043', $products);
		$this->assertArrayHasKey('IND00232', $products);
		$this->assertArrayHasKey('GFX00067', $products);
		$this->assertArrayHasKey('PHN00098', $products);
		
		//assert objects
		$this->assertInstanceOf('Acme\Entity\Product', $products['IND00054']);
		$this->assertEquals(1, $products['IND00054']->id);
		
		$this->assertInstanceOf('Acme\Entity\Product', $products['IND00043']);
		$this->assertEquals(2, $products['IND00043']->id);
		
		$this->assertInstanceOf('Acme\Entity\Product', $products['IND00232']);
		$this->assertEquals(3, $products['IND00232']->id);
		
		$this->assertInstanceOf('Acme\Entity\Product', $products['GFX00067']);
		$this->assertEquals(4, $products['GFX00067']->id);
		
		$this->assertInstanceOf('Acme\Entity\Product', $products['PHN00098']);
		$this->assertEquals(5, $products['PHN00098']->id);
	}
	
	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testIndexMissingColumn() {
		$products = $this->productsManager->index(Column::description())->find();
	}
	
	public function testIndexCallback() {
		$products = $this->productsManager->index_callback(function($product) {
			return "prod_{$product->id}";
		})->find();
		
		$this->assertCount(5, $products);
		
		//assert indexes
		$this->assertArrayHasKey('prod_1', $products);
		$this->assertArrayHasKey('prod_2', $products);
		$this->assertArrayHasKey('prod_3', $products);
		$this->assertArrayHasKey('prod_4', $products);
		$this->assertArrayHasKey('prod_5', $products);
		
		//assert objects
		$this->assertInstanceOf('Acme\Entity\Product', $products['prod_1']);
		$this->assertEquals('IND00054', $products['prod_1']->code);
		
		$this->assertInstanceOf('Acme\Entity\Product', $products['prod_2']);
		$this->assertEquals('IND00043', $products['prod_2']->code);
		
		$this->assertInstanceOf('Acme\Entity\Product', $products['prod_3']);
		$this->assertEquals('IND00232', $products['prod_3']->code);
		
		$this->assertInstanceOf('Acme\Entity\Product', $products['prod_4']);
		$this->assertEquals('GFX00067', $products['prod_4']->code);
		
		$this->assertInstanceOf('Acme\Entity\Product', $products['prod_5']);
		$this->assertEquals('PHN00098', $products['prod_5']->code);
	}
	
	/*
	 * GROUPING
	 */
	public function testGroup() {
		$products = $this->productsManager->group(Attr::category())->find();
		$this->assertCount(3, $products);
		
		//assert groups
		$this->assertArrayHasKey('Clothes', $products);
		$this->assertArrayHasKey('Hardware', $products);
		$this->assertArrayHasKey('Smartphones', $products);
		
		//assert values
		$this->assertCount(3, $products['Clothes']);
		$this->assertCount(1, $products['Hardware']);
		$this->assertCount(1, $products['Smartphones']);
	}
	
	public function testGroupIndex() {
		$products = $this->productsManager
		->group(Attr::category())
		->index(Attr::id())
		->find();
		
		$this->assertCount(3, $products);
		
		//assert groups
		$this->assertArrayHasKey('Clothes', $products);
		$this->assertArrayHasKey('Hardware', $products);
		$this->assertArrayHasKey('Smartphones', $products);
		
		//assert values
		$this->assertCount(3, $products['Clothes']);
		$this->assertArrayHasKey(1, $products['Clothes']);
		$this->assertArrayHasKey(2, $products['Clothes']);
		$this->assertArrayHasKey(3, $products['Clothes']);
		
		$this->assertCount(1, $products['Hardware']);
		$this->assertArrayHasKey(4, $products['Hardware']);
		
		$this->assertCount(1, $products['Smartphones']);
		$this->assertArrayHasKey(5, $products['Smartphones']);
		
		//assert objects
		$this->assertEquals(1, $products['Clothes'][1]->id);
		$this->assertEquals(2, $products['Clothes'][2]->id);
		$this->assertEquals(3, $products['Clothes'][3]->id);
		$this->assertEquals(4, $products['Hardware'][4]->id);
		$this->assertEquals(5, $products['Smartphones'][5]->id);
	}
	
	public function testGroupColumn() {
		$products = $this->productsManager->group(Column::category())->find();
		$this->assertCount(3, $products);
	
		//assert groups
		$this->assertArrayHasKey('Clothes', $products);
		$this->assertArrayHasKey('Hardware', $products);
		$this->assertArrayHasKey('Smartphones', $products);
	
		//assert values
		$this->assertCount(3, $products['Clothes']);
		$this->assertCount(1, $products['Hardware']);
		$this->assertCount(1, $products['Smartphones']);
	}
	
	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testGroupMissingColumn() {
		$products = $this->productsManager->group(Column::manufacture_year())->find();
	}
	
	public function testGroupCallback() {
		$products = $this->productsManager
		->group_callback(function ($product) {
			return substr($product->code, 0, 3);
		})
		->find();
		$this->assertCount(3, $products);
		
		$this->assertArrayHasKey('IND', $products);
		$this->assertArrayHasKey('GFX', $products);
		$this->assertArrayHasKey('PHN', $products);
	}
}
?>