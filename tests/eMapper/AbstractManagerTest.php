<?php
namespace eMapper;

use eMapper\Query\Attr;
use eMapper\Query\Column;
use eMapper\Query\Cond as Q;
use eMapper\Engine\Generic\Driver;
use Acme\Entity\Product;

abstract class AbstractManagerTest extends MapperTest {	
	/**
	 * Test manager
	 * @var Manager
	 */
	protected $productsManager;
	
	public function setUp() {
		$this->mapper = $this->getMapper();
		$this->productsManager = $this->mapper->newManager('Acme\Entity\Product'); 
	}
	
	public function testFindByPk() {
		$product = $this->productsManager
		->debug(function($q) {
			//echo "$q\n";
		})
		->findByPK(1);
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
		$this->assertCount(8, $products);
	
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
		$this->assertCount(5, $products);
	}
	
	public function testFilter() {
		$products = $this->productsManager->filter(Attr::id()->lt(3))->find();
		$this->assertCount(2, $products);
	}
	
	public function testExclude() {
		$products = $this->productsManager->exclude(Attr::id()->lt(3))->find();
		$this->assertCount(6, $products);
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
		$this->assertCount(5, $products);
	}
	
	public function testColumnReverseCondition() {
		$products = $this->productsManager->find(Column::manufacture_year()->eq(2013, false));
		$this->assertCount(3, $products);
	}
	
	/**
	 * Tests setting an OR condition using the Q class
	 */
	public function testOrCondition() {
		$products = $this->productsManager->find(Q::orfilter(Attr::id()->eq(5), Attr::id()->eq(3)));
		$this->assertCount(2, $products);
	}
	
	/*
	 * FILTERS
	 */
	public function testAndFilter() {
		$products = $this->productsManager
		->filter(Attr::category()->eq('Smartphones'), Attr::price()->lt(310))
		->find();
		$this->assertCount(1, $products);
		$this->assertInstanceOf('Acme\Entity\Product', $products[0]);
		$this->assertEquals(5, $products[0]->id);
	}
	
	public function testNegatedAndFilter() {
		$products = $this->productsManager
		->exclude(Attr::category()->eq('Smartphones'), Attr::price()->lt(350))
		->find();
		$this->assertCount(6, $products);
	}
	
	public function testOrFilter() {
		$products = $this->productsManager
		->orfilter(Attr::category()->eq('Smartphones'), Attr::price()->gt(310))
		->find();
		$this->assertCount(3, $products);
	}
	
	public function testNegatedOrFilter() {
		$products = $this->productsManager
		->orfilter(Attr::category()->eq('Smartphones'), Attr::price()->gt(310))
		->negate()
		->find();
		$this->assertCount(5, $products);
	}
	
	/*
	 * INDEXATION
	 */
	
	public function testIndex() {
		$products = $this->productsManager->index(Attr::id())->find();
		$this->assertCount(8, $products);
		
		//assert indexes
		$this->assertArrayHasKey(1, $products);
		$this->assertArrayHasKey(2, $products);
		$this->assertArrayHasKey(3, $products);
		$this->assertArrayHasKey(4, $products);
		$this->assertArrayHasKey(5, $products);
		$this->assertArrayHasKey(6, $products);
		$this->assertArrayHasKey(7, $products);
		$this->assertArrayHasKey(8, $products);
		
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
		
		$this->assertInstanceOf('Acme\Entity\Product', $products[6]);
		$this->assertEquals('TEC00103', $products[6]->code);
		
		$this->assertInstanceOf('Acme\Entity\Product', $products[7]);
		$this->assertEquals('PHN00666', $products[7]->code);
		
		$this->assertInstanceOf('Acme\Entity\Product', $products[8]);
		$this->assertEquals('SOFT0134', $products[8]->code);
	}
	
	public function testIndexType() {
		$products = $this->productsManager->index(Attr::id('string'))->find();
		$this->assertCount(8, $products);
	
		//assert indexes
		$this->assertArrayHasKey('1', $products);
		$this->assertArrayHasKey('2', $products);
		$this->assertArrayHasKey('3', $products);
		$this->assertArrayHasKey('4', $products);
		$this->assertArrayHasKey('5', $products);
		$this->assertArrayHasKey('6', $products);
		$this->assertArrayHasKey('7', $products);
		$this->assertArrayHasKey('8', $products);
	
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
		
		$this->assertInstanceOf('Acme\Entity\Product', $products['6']);
		$this->assertEquals('TEC00103', $products['6']->code);
		
		$this->assertInstanceOf('Acme\Entity\Product', $products['7']);
		$this->assertEquals('PHN00666', $products['7']->code);
		
		$this->assertInstanceOf('Acme\Entity\Product', $products['8']);
		$this->assertEquals('SOFT0134', $products['8']->code);
	}
	
	public function testIndexColumn() {
		$products = $this->productsManager->index(Column::product_code())->find();
		$this->assertCount(8, $products);
		
		//assert indexes
		$this->assertArrayHasKey('IND00054', $products);
		$this->assertArrayHasKey('IND00043', $products);
		$this->assertArrayHasKey('IND00232', $products);
		$this->assertArrayHasKey('GFX00067', $products);
		$this->assertArrayHasKey('PHN00098', $products);
		$this->assertArrayHasKey('TEC00103', $products);
		$this->assertArrayHasKey('PHN00666', $products);
		$this->assertArrayHasKey('SOFT0134', $products);
		
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
		
		$this->assertInstanceOf('Acme\Entity\Product', $products['TEC00103']);
		$this->assertEquals(6, $products['TEC00103']->id);
		
		$this->assertInstanceOf('Acme\Entity\Product', $products['PHN00666']);
		$this->assertEquals(7, $products['PHN00666']->id);
		
		$this->assertInstanceOf('Acme\Entity\Product', $products['SOFT0134']);
		$this->assertEquals(8, $products['SOFT0134']->id);
	}
	
	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testIndexMissingColumn() {
		$products = $this->productsManager->index(Column::description())->find();
	}
	
	public function testIndexCallback() {
		$products = $this->productsManager->indexCallback(function($product) {
			return "prod_{$product->id}";
		})->find();
		
		$this->assertCount(8, $products);
		
		//assert indexes
		$this->assertArrayHasKey('prod_1', $products);
		$this->assertArrayHasKey('prod_2', $products);
		$this->assertArrayHasKey('prod_3', $products);
		$this->assertArrayHasKey('prod_4', $products);
		$this->assertArrayHasKey('prod_5', $products);
		$this->assertArrayHasKey('prod_6', $products);
		$this->assertArrayHasKey('prod_7', $products);
		$this->assertArrayHasKey('prod_8', $products);
		
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
		
		$this->assertInstanceOf('Acme\Entity\Product', $products['prod_6']);
		$this->assertEquals('TEC00103', $products['prod_6']->code);
		
		$this->assertInstanceOf('Acme\Entity\Product', $products['prod_7']);
		$this->assertEquals('PHN00666', $products['prod_7']->code);
		
		$this->assertInstanceOf('Acme\Entity\Product', $products['prod_8']);
		$this->assertEquals('SOFT0134', $products['prod_8']->code);
	}
	
	/*
	 * GROUPING
	 */
	public function testGroup() {
		$products = $this->productsManager->group(Attr::category())->find();
		$this->assertCount(5, $products);
		
		//assert groups
		$this->assertArrayHasKey('Clothes', $products);
		$this->assertArrayHasKey('Hardware', $products);
		$this->assertArrayHasKey('Smartphones', $products);
		$this->assertArrayHasKey('Laptops', $products);
		$this->assertArrayHasKey('Software', $products);
		
		//assert values
		$this->assertCount(3, $products['Clothes']);
		$this->assertCount(1, $products['Hardware']);
		$this->assertCount(2, $products['Smartphones']);
		$this->assertCount(1, $products['Laptops']);
		$this->assertCount(1, $products['Software']);
	}
	
	public function testGroupIndex() {
		$products = $this->productsManager
		->group(Attr::category())
		->index(Attr::id())
		->find();
		
		$this->assertCount(5, $products);
		
		//assert groups
		$this->assertArrayHasKey('Clothes', $products);
		$this->assertArrayHasKey('Hardware', $products);
		$this->assertArrayHasKey('Smartphones', $products);
		$this->assertArrayHasKey('Laptops', $products);
		$this->assertArrayHasKey('Software', $products);
		
		//assert values
		$this->assertCount(3, $products['Clothes']);
		$this->assertArrayHasKey(1, $products['Clothes']);
		$this->assertArrayHasKey(2, $products['Clothes']);
		$this->assertArrayHasKey(3, $products['Clothes']);
		
		$this->assertCount(1, $products['Hardware']);
		$this->assertArrayHasKey(4, $products['Hardware']);
		
		$this->assertCount(2, $products['Smartphones']);
		$this->assertArrayHasKey(5, $products['Smartphones']);
		$this->assertArrayHasKey(7, $products['Smartphones']);
		
		$this->assertCount(1, $products['Laptops']);
		$this->assertArrayHasKey(6, $products['Laptops']);
		
		$this->assertCount(1, $products['Software']);
		$this->assertArrayHasKey(8, $products['Software']);
		
		//assert objects
		$this->assertEquals(1, $products['Clothes'][1]->id);
		$this->assertEquals(2, $products['Clothes'][2]->id);
		$this->assertEquals(3, $products['Clothes'][3]->id);
		$this->assertEquals(4, $products['Hardware'][4]->id);
		$this->assertEquals(5, $products['Smartphones'][5]->id);
		$this->assertEquals(6, $products['Laptops'][6]->id);
		$this->assertEquals(7, $products['Smartphones'][7]->id);
		$this->assertEquals(8, $products['Software'][8]->id);
	}
	
	public function testGroupColumn() {
		$products = $this->productsManager->group(Column::category())->find();
		$this->assertCount(5, $products);
	
		//assert groups
		$this->assertArrayHasKey('Clothes', $products);
		$this->assertArrayHasKey('Hardware', $products);
		$this->assertArrayHasKey('Smartphones', $products);
		$this->assertArrayHasKey('Laptops', $products);
		$this->assertArrayHasKey('Software', $products);
	
		//assert values
		$this->assertCount(3, $products['Clothes']);
		$this->assertCount(1, $products['Hardware']);
		$this->assertCount(2, $products['Smartphones']);
		$this->assertCount(1, $products['Laptops']);
		$this->assertCount(1, $products['Software']);
	}
	
	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testGroupMissingColumn() {
		$products = $this->productsManager->group(Column::manufacture_year())->find();
	}
	
	public function testGroupCallback() {
		$products = $this->productsManager
		->groupCallback(function ($product) {
			return substr($product->code, 0, 3);
		})
		->find();
		$this->assertCount(5, $products);
		
		$this->assertArrayHasKey('IND', $products);
		$this->assertArrayHasKey('GFX', $products);
		$this->assertArrayHasKey('PHN', $products);
		$this->assertArrayHasKey('TEC', $products);
		$this->assertArrayHasKey('SOF', $products);
	}
		
	/**
	 * AGGREGATE FUNCTIONS
	 */
	
	public function testCount() {
		$totalProducts = $this->productsManager->count();
		$this->assertInternalType('integer', $totalProducts);
		$this->assertEquals(8, $totalProducts);
		
		$total = $this->productsManager
		->filter(Attr::category()->eq('Clothes'))->count();
		$this->assertEquals(3, $total);
		
		$total = $this->productsManager->filter(Attr::category()->eq('Clothes'))->type('f')->count();
		$this->assertInternalType('float', $total);
		$this->assertEquals(3, $total);
	}
	
	public function testAverage() {
		$avg = $this->productsManager->avg(Column::price());
		$this->assertInternalType('float', $avg);
		$this->assertEquals(252.0, floor($avg));
		
		$avg = $this->productsManager->filter(Attr::category()->eq('Clothes', false))->type('i')->avg(Column::price());
		$this->assertInternalType('integer', $avg);
		$this->assertEquals(312, $avg);
	}
	
	public function testMax() {
		$max = $this->productsManager->max(Column::price());
		$this->assertInternalType('float', $max);
		$this->assertEquals(550, floor($max));
		
		$max = $this->productsManager->filter(Attr::category()->eq('Clothes', false))->type('i')->max(Column::price());
		$this->assertInternalType('integer', $max);
		$this->assertEquals(550, $max);
	}
	
	public function testMin() {
		$min = $this->productsManager->min(Column::price());
		$this->assertInternalType('float', $min);
		$this->assertEquals(70, floor($min));
		
		$min = $this->productsManager->filter(Attr::category()->eq('Clothes'))->type('i')->min(Column::rating());
		$this->assertInternalType('integer', $min);
		$this->assertEquals(3, $min);
	}
	
	public function testSum() {
		$sum = $this->productsManager->sum(Column::price());
		$this->assertInternalType('float', $sum);
		$this->assertEquals(2019.0, floor($sum));
		
		$sum = $this->productsManager->filter(Attr::category()->eq('Clothes'))->type('i')->sum(Column::price());
		$this->assertInternalType('integer', $sum);
		$this->assertEquals(457, floor($sum));
	}
	
	/*
	 * SQL TESTS
	 */
	public function testSQLFindByPk() {
		$product = $this->productsManager
		->debug(function ($query) {
			$this->assertEquals('SELECT product_id,product_code,price,category,color FROM products WHERE product_id = 1', $query);
		})
		->findByPK(1);
	}
	
	public function testSQLAll() {
		$query = "SELECT _t.product_id,_t.product_code,_t.price,_t.category,_t.color FROM products _t";
		$this->productsManager
		->debug(function($q) use ($query) {
			$this->assertEquals($query, $q);
		})
		->find();
	}
	
	public function testSQLAttrs() {
		$query = "SELECT _t.product_code,_t.category FROM products _t";
		$this->productsManager
		->debug(function($q) use ($query) {
			$this->assertEquals($query, $q);
		})
		->attrs(Attr::code(), Attr::category())
		->find();
	}
	
	public function testSQLOrder() {
		$query = "SELECT _t.product_id,_t.product_code,_t.price,_t.category,_t.color FROM products _t ORDER BY _t.product_code,_t.category DESC";
		$this->productsManager
		->debug(function($q) use ($query) {
			$this->assertEquals($query, $q);
		})
		->orderBy(Attr::code(), Attr::category()->type('DESC'))
		->find();
	}
	
	public function testSQLLimit() {
		$query = "SELECT _t.product_id,_t.product_code,_t.price,_t.category,_t.color FROM products _t LIMIT 10";
		$this->productsManager
		->debug(function($q) use ($query) {
			$this->assertEquals($query, $q);
		})
		->limit(10)
		->find();
	}
	
	public function testSQLOffset() {
		$query = "SELECT _t.product_id,_t.product_code,_t.price,_t.category,_t.color FROM products _t LIMIT 10 OFFSET 5";
		$this->productsManager
		->debug(function($q) use ($query) {
			$this->assertEquals($query, $q);
		})
		->limit(10)
		->offset(5)
		->find();
	}
	
	public function testSQLDistinct() {
		$query = "SELECT DISTINCT _t.product_id,_t.product_code,_t.price,_t.category,_t.color FROM products _t";
		$this->productsManager
		->debug(function($q) use ($query) {
			$this->assertEquals($query, $q);
		})
		->distinct()
		->find();
	}
	
	public function testSQLEq() {
		$query = "SELECT _t.product_id,_t.product_code,_t.price,_t.category,_t.color FROM products _t WHERE _t.product_code = 'XXX001'";
		$this->productsManager
		->debug(function($q) use ($query) {
			$this->assertEquals($query, $q);
		})
		->find(Attr::code()->eq('XXX001'));
	}
	
	public function testSQLNotEq() {
		$query = "SELECT _t.product_id,_t.product_code,_t.price,_t.category,_t.color FROM products _t WHERE _t.product_code <> 'XXX001'";
		$this->productsManager
		->debug(function($q) use ($query) {
			$this->assertEquals($query, $q);
		})
		->find(Attr::code()->eq('XXX001', false));
	}
	
	public function testSQLContains() {
		$query = "SELECT _t.product_id,_t.product_code,_t.price,_t.category,_t.color FROM products _t WHERE _t.product_code LIKE '%GFX%'";
		$this->productsManager
		->debug(function($q) use ($query) {
			$this->assertEquals($query, $q);
		})
		->find(Attr::code()->contains('GFX'));
	}
	
	public function testSQLNotContains() {
		$query = "SELECT _t.product_id,_t.product_code,_t.price,_t.category,_t.color FROM products _t WHERE _t.product_code NOT LIKE '%GFX%'";
		$this->productsManager
		->debug(function($q) use ($query) {
			$this->assertEquals($query, $q);
		})
		->find(Attr::code()->contains('GFX', false));
	}
	
	public function testSQLiContains() {
		$query = "SELECT _t.product_id,_t.product_code,_t.price,_t.category,_t.color FROM products _t WHERE LOWER(_t.product_code) LIKE LOWER('%GFX%')";
		$this->productsManager
		->debug(function($q) use ($query) {
			$this->assertEquals($query, $q);
		})
		->find(Attr::code()->icontains('GFX'));
	}
	
	public function testSQLNotiContains() {
		$query = "SELECT _t.product_id,_t.product_code,_t.price,_t.category,_t.color FROM products _t WHERE LOWER(_t.product_code) NOT LIKE LOWER('%GFX%')";
		$this->productsManager
		->debug(function($q) use ($query) {
			$this->assertEquals($query, $q);
		})
		->find(Attr::code()->icontains('GFX', false));
	}
	
	public function testSQLIn() {
		$query = "SELECT _t.product_id,_t.product_code,_t.price,_t.category,_t.color FROM products _t WHERE _t.product_id IN (3,4)";
		$this->productsManager
		->debug(function($q) use ($query) {
			$this->assertEquals($query, $q);
		})
		->find(Attr::id()->in([3, 4]));
	}
	
	public function testSQLNotIn() {
		$query = "SELECT _t.product_id,_t.product_code,_t.price,_t.category,_t.color FROM products _t WHERE _t.product_id NOT IN (3,4)";
		$this->productsManager
		->debug(function($q) use ($query) {
			$this->assertEquals($query, $q);
		})
		->find(Attr::id()->in([3, 4], false));
	}
	
	public function testSQLGreaterThan() {
		$query = "SELECT _t.product_id,_t.product_code,_t.price,_t.category,_t.color FROM products _t WHERE _t.product_id > 3";
		$this->productsManager
		->debug(function($q) use ($query) {
			$this->assertEquals($query, $q);
		})
		->find(Attr::id()->gt(3));
	}
	
	public function testSQLNotGreaterThan() {
		$query = "SELECT _t.product_id,_t.product_code,_t.price,_t.category,_t.color FROM products _t WHERE _t.product_id <= 3";
		$this->productsManager
		->debug(function($q) use ($query) {
			$this->assertEquals($query, $q);
		})
		->find(Attr::id()->gt(3, false));
	}
	
	public function testSQLGreaterThanEqual() {
		$query = "SELECT _t.product_id,_t.product_code,_t.price,_t.category,_t.color FROM products _t WHERE _t.product_id >= 3";
		$this->productsManager
		->debug(function($q) use ($query) {
			$this->assertEquals($query, $q);
		})
		->find(Attr::id()->gte(3));
	}
	
	public function testSQLNotGreaterThanEqual() {
		$query = "SELECT _t.product_id,_t.product_code,_t.price,_t.category,_t.color FROM products _t WHERE _t.product_id < 3";
		$this->productsManager
		->debug(function($q) use ($query) {
			$this->assertEquals($query, $q);
		})
		->find(Attr::id()->gte(3, false));
	}
	
	public function testSQLLessThan() {
		$query = "SELECT _t.product_id,_t.product_code,_t.price,_t.category,_t.color FROM products _t WHERE _t.product_id < 3";
		$this->productsManager
		->debug(function($q) use ($query) {
			$this->assertEquals($query, $q);
		})
		->find(Attr::id()->lt(3));
	}
	
	public function testSQLNotLessThan() {
		$query = "SELECT _t.product_id,_t.product_code,_t.price,_t.category,_t.color FROM products _t WHERE _t.product_id >= 3";
		$this->productsManager
		->debug(function($q) use ($query) {
			$this->assertEquals($query, $q);
		})
		->find(Attr::id()->lt(3, false));
	}
	
	public function testSQLLessThanEqual() {
		$query = "SELECT _t.product_id,_t.product_code,_t.price,_t.category,_t.color FROM products _t WHERE _t.product_id <= 3";
		$this->productsManager
		->debug(function($q) use ($query) {
			$this->assertEquals($query, $q);
		})
		->find(Attr::id()->lte(3));
	}
	
	public function testSQLNotLessThanEqual() {
		$query = "SELECT _t.product_id,_t.product_code,_t.price,_t.category,_t.color FROM products _t WHERE _t.product_id > 3";
		$this->productsManager
		->debug(function($q) use ($query) {
			$this->assertEquals($query, $q);
		})
		->find(Attr::id()->lte(3, false));
	}
	
	public function testSQLStartsWith() {
		$query = "SELECT _t.product_id,_t.product_code,_t.price,_t.category,_t.color FROM products _t WHERE _t.product_code LIKE 'IND%'";
		$this->productsManager
		->debug(function($q) use ($query) {
			$this->assertEquals($query, $q);
		})
		->find(Attr::code()->startswith('IND'));
	}
	
	public function testSQLNotStartsWith() {
		$query = "SELECT _t.product_id,_t.product_code,_t.price,_t.category,_t.color FROM products _t WHERE _t.product_code NOT LIKE 'IND%'";
		$this->productsManager
		->debug(function($q) use ($query) {
			$this->assertEquals($query, $q);
		})
		->find(Attr::code()->startswith('IND', false));
	}
	
	public function testSQLiStartsWith() {
		$query = "SELECT _t.product_id,_t.product_code,_t.price,_t.category,_t.color FROM products _t WHERE LOWER(_t.product_code) LIKE LOWER('IND%')";
		$this->productsManager
		->debug(function($q) use ($query) {
			$this->assertEquals($query, $q);
		})
		->find(Attr::code()->istartswith('IND'));
	}
	
	public function testSQLNotiStartsWith() {
		$query = "SELECT _t.product_id,_t.product_code,_t.price,_t.category,_t.color FROM products _t WHERE LOWER(_t.product_code) NOT LIKE LOWER('IND%')";
		$this->productsManager
		->debug(function($q) use ($query) {
			$this->assertEquals($query, $q);
		})
		->find(Attr::code()->istartswith('IND', false));
	}
	
	public function testSQLEndsWith() {
		$query = "SELECT _t.product_id,_t.product_code,_t.price,_t.category,_t.color FROM products _t WHERE _t.product_code LIKE '%232'";
		$this->productsManager
		->debug(function($q) use ($query) {
			$this->assertEquals($query, $q);
		})
		->find(Attr::code()->endswith('232'));
	}
	
	public function testSQLNotEndsWith() {
		$query = "SELECT _t.product_id,_t.product_code,_t.price,_t.category,_t.color FROM products _t WHERE _t.product_code NOT LIKE '%232'";
		$this->productsManager
		->debug(function($q) use ($query) {
			$this->assertEquals($query, $q);
		})
		->find(Attr::code()->endswith('232', false));
	}
	
	public function testSQLiEndsWith() {
		$query = "SELECT _t.product_id,_t.product_code,_t.price,_t.category,_t.color FROM products _t WHERE LOWER(_t.product_code) LIKE LOWER('%232')";
		$this->productsManager
		->debug(function($q) use ($query) {
			$this->assertEquals($query, $q);
		})
		->find(Attr::code()->iendswith('232'));
	}
	
	public function testSQLNotiEndsWith() {
		$query = "SELECT _t.product_id,_t.product_code,_t.price,_t.category,_t.color FROM products _t WHERE LOWER(_t.product_code) NOT LIKE LOWER('%232')";
		$this->productsManager
		->debug(function($q) use ($query) {
			$this->assertEquals($query, $q);
		})
		->find(Attr::code()->iendswith('232', false));
	}
	
	public function testSQLRange() {
		$query = "SELECT _t.product_id,_t.product_code,_t.price,_t.category,_t.color FROM products _t WHERE _t.product_id BETWEEN 2 AND 4";
		$this->productsManager
		->debug(function($q) use ($query) {
			$this->assertEquals($query, $q);
		})
		->find(Attr::id()->range(2, 4));
	}
	
	public function testSQLNotRange() {
		$query = "SELECT _t.product_id,_t.product_code,_t.price,_t.category,_t.color FROM products _t WHERE _t.product_id NOT BETWEEN 2 AND 4";
		$this->productsManager
		->debug(function($q) use ($query) {
			$this->assertEquals($query, $q);
		})
		->find(Attr::id()->range(2, 4, false));
	}
	
	public function testSQLIsNull() {
		$query = "SELECT _t.product_id,_t.product_code,_t.price,_t.category,_t.color FROM products _t WHERE _t.color IS NULL";
		$this->productsManager
		->debug(function($q) use ($query) {
			$this->assertEquals($query, $q);
		})
		->find(Attr::color()->isnull());
	}
	
	public function testSQLIsNotNull() {
		$query = "SELECT _t.product_id,_t.product_code,_t.price,_t.category,_t.color FROM products _t WHERE _t.color IS NOT NULL";
		$this->productsManager
		->debug(function($q) use ($query) {
			$this->assertEquals($query, $q);
		})
		->find(Attr::color()->isnull(false));
	}
}