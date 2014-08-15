<?php
namespace eMapper\PostgreSQL\Mapper\ArrayMapper;

use eMapper\PostgreSQL\PostgreSQLConfig;
use eMapper\Mapper\ArrayMapper\AbstractArrayMapperTest;
use eMapper\Result\ArrayType;

/**
 * Tests Mapper class mapping to array values
 * @author emaphp
 * @group postgre
 * @group mapper
 */
class ArrayMapperTest extends AbstractArrayMapperTest {
	use PostgreSQLConfig;
	
	public function testIndexOverrideList() {
		//PGSQL_BOTH
		$products = $this->mapper->type('array[category]')->query("SELECT * FROM products ORDER BY product_id ASC");
		$this->assertInternalType('array', $products);
		$this->assertCount(3, $products);
		$this->assertArrayHasKey('Clothes', $products);
		$this->assertArrayHasKey('Hardware', $products);
		$this->assertArrayHasKey('Smartphones', $products);
	
		$this->assertInternalType('array', $products['Clothes']);
	
		////
		$this->assertArrayHasKey('product_id', $products['Clothes']);
		$this->assertArrayHasKey(0, $products['Clothes']);
		$this->assertInternalType('integer', $products['Clothes']['product_id']);
		$this->assertEquals(3, $products['Clothes']['product_id']);
	
		$this->assertArrayHasKey('product_code', $products['Clothes']);
		$this->assertArrayHasKey(1, $products['Clothes']);
		$this->assertInternalType('string', $products['Clothes']['product_code']);
		$this->assertEquals('IND00232', $products['Clothes']['product_code']);
	
		$this->assertArrayHasKey('description', $products['Clothes']);
		$this->assertArrayHasKey(2, $products['Clothes']);
		$this->assertInternalType('string', $products['Clothes']['description']);
		$this->assertEquals('Green shirt', $products['Clothes']['description']);
	
		$this->assertArrayHasKey('color', $products['Clothes']);
		$this->assertArrayHasKey(3, $products['Clothes']);
		$this->assertInternalType('string', $products['Clothes']['color']);
		$this->assertEquals('707c04', $products['Clothes']['color']);
	
		$this->assertArrayHasKey('price', $products['Clothes']);
		$this->assertArrayHasKey(4, $products['Clothes']);
		$this->assertInternalType('float', $products['Clothes']['price']);
		$this->assertEquals(70.9, $products['Clothes']['price']);
	
		$this->assertArrayHasKey('category', $products['Clothes']);
		$this->assertArrayHasKey(5, $products['Clothes']);
		$this->assertInternalType('string', $products['Clothes']['category']);
		$this->assertEquals('Clothes', $products['Clothes']['category']);
	
		$this->assertArrayHasKey('rating', $products['Clothes']);
		$this->assertArrayHasKey(6, $products['Clothes']);
		$this->assertInternalType('float', $products['Clothes']['rating']);
		$this->assertEquals(4.1, $products['Clothes']['rating']);
	
		$this->assertArrayHasKey('refurbished', $products['Clothes']);
		$this->assertArrayHasKey(7, $products['Clothes']);
		$this->assertInternalType('boolean', $products['Clothes']['refurbished']);
		$this->assertFalse($products['Clothes']['refurbished']);
	
		$this->assertArrayHasKey('manufacture_year', $products['Clothes']);
		$this->assertArrayHasKey(8, $products['Clothes']);
		$this->assertInternalType('integer', $products['Clothes']['manufacture_year']);
		$this->assertEquals(2013, $products['Clothes']['manufacture_year']);
	
		////
		$this->assertInternalType('array', $products['Hardware']);
		$this->assertArrayHasKey('product_id', $products['Hardware']);
		$this->assertInternalType('integer', $products['Hardware']['product_id']);
		$this->assertEquals(4, $products['Hardware']['product_id']);
	
		$this->assertArrayHasKey('product_code', $products['Hardware']);
		$this->assertInternalType('string', $products['Hardware']['product_code']);
		$this->assertEquals('GFX00067', $products['Hardware']['product_code']);
	
		$this->assertArrayHasKey('description', $products['Hardware']);
		$this->assertInternalType('string', $products['Hardware']['description']);
		$this->assertEquals('ATI HD 9999', $products['Hardware']['description']);
	
		$this->assertArrayHasKey('color', $products['Hardware']);
		$this->assertNull($products['Hardware']['color']);
	
		$this->assertArrayHasKey('price', $products['Hardware']);
		$this->assertInternalType('float', $products['Hardware']['price']);
		$this->assertEquals(120.75, $products['Hardware']['price']);
	
		$this->assertArrayHasKey('category', $products['Hardware']);
		$this->assertInternalType('string', $products['Hardware']['category']);
		$this->assertEquals('Hardware', $products['Hardware']['category']);
	
		$this->assertArrayHasKey('rating', $products['Hardware']);
		$this->assertInternalType('float', $products['Hardware']['rating']);
		$this->assertEquals(3.8, $products['Hardware']['rating']);
	
		$this->assertArrayHasKey('refurbished', $products['Hardware']);
		$this->assertInternalType('boolean', $products['Hardware']['refurbished']);
		$this->assertFalse($products['Hardware']['refurbished']);
	
		$this->assertArrayHasKey('manufacture_year', $products['Hardware']);
		$this->assertInternalType('integer', $products['Hardware']['manufacture_year']);
		$this->assertEquals(2013, $products['Hardware']['manufacture_year']);
	
		////
		$this->assertInternalType('array', $products['Smartphones']);
		$this->assertArrayHasKey('product_id', $products['Smartphones']);
		$this->assertInternalType('integer', $products['Smartphones']['product_id']);
		$this->assertEquals(5, $products['Smartphones']['product_id']);
	
		$this->assertArrayHasKey('product_code', $products['Smartphones']);
		$this->assertInternalType('string', $products['Smartphones']['product_code']);
		$this->assertEquals('PHN00098', $products['Smartphones']['product_code']);
	
		$this->assertArrayHasKey('description', $products['Smartphones']);
		$this->assertInternalType('string', $products['Smartphones']['description']);
		$this->assertEquals('Android phone', $products['Smartphones']['description']);
	
		$this->assertArrayHasKey('color', $products['Smartphones']);
		$this->assertInternalType('string', $products['Smartphones']['color']);
		$this->assertEquals('00a7eb', $products['Smartphones']['color']);
	
		$this->assertArrayHasKey('price', $products['Smartphones']);
		$this->assertInternalType('float', $products['Smartphones']['price']);
		$this->assertEquals(300.3, $products['Smartphones']['price']);
	
		$this->assertArrayHasKey('category', $products['Smartphones']);
		$this->assertInternalType('string', $products['Smartphones']['category']);
		$this->assertEquals('Smartphones', $products['Smartphones']['category']);
	
		$this->assertArrayHasKey('rating', $products['Smartphones']);
		$this->assertInternalType('float', $products['Smartphones']['rating']);
		$this->assertEquals(4.8, $products['Smartphones']['rating']);
	
		$this->assertArrayHasKey('refurbished', $products['Smartphones']);
		$this->assertInternalType('boolean', $products['Smartphones']['refurbished']);
		$this->assertTrue($products['Smartphones']['refurbished']);
	
		$this->assertArrayHasKey('manufacture_year', $products['Smartphones']);
		$this->assertInternalType('integer', $products['Smartphones']['manufacture_year']);
		$this->assertEquals(2011, $products['Smartphones']['manufacture_year']);
	
		//PGSQL_ASSOC
		$products = $this->mapper->type('array[category]', ArrayType::ASSOC)->query("SELECT * FROM products ORDER BY product_id ASC");
	
		$this->assertArrayHasKey('product_id', $products['Clothes']);
		$this->assertArrayHasKey('product_code', $products['Clothes']);
		$this->assertArrayHasKey('description', $products['Clothes']);
		$this->assertArrayHasKey('color', $products['Clothes']);
		$this->assertArrayHasKey('price', $products['Clothes']);
		$this->assertArrayHasKey('category', $products['Clothes']);
		$this->assertArrayHasKey('rating', $products['Clothes']);
		$this->assertArrayHasKey('refurbished', $products['Clothes']);
		$this->assertArrayHasKey('manufacture_year', $products['Clothes']);
		$this->assertArrayNotHasKey(0, $products['Clothes']);
		$this->assertArrayNotHasKey(1, $products['Clothes']);
		$this->assertArrayNotHasKey(2, $products['Clothes']);
		$this->assertArrayNotHasKey(3, $products['Clothes']);
		$this->assertArrayNotHasKey(4, $products['Clothes']);
		$this->assertArrayNotHasKey(5, $products['Clothes']);
		$this->assertArrayNotHasKey(6, $products['Clothes']);
		$this->assertArrayNotHasKey(7, $products['Clothes']);
		$this->assertArrayNotHasKey(8, $products['Clothes']);
	
		//PGSQL_NUM
		$products = $this->mapper->type('array[5]', ArrayType::NUM)->query("SELECT * FROM products ORDER BY product_id ASC");
	
		$this->assertArrayNotHasKey('product_id', $products['Clothes']);
		$this->assertArrayNotHasKey('product_code', $products['Clothes']);
		$this->assertArrayNotHasKey('description', $products['Clothes']);
		$this->assertArrayNotHasKey('color', $products['Clothes']);
		$this->assertArrayNotHasKey('price', $products['Clothes']);
		$this->assertArrayNotHasKey('category', $products['Clothes']);
		$this->assertArrayNotHasKey('rating', $products['Clothes']);
		$this->assertArrayNotHasKey('refurbished', $products['Clothes']);
		$this->assertArrayNotHasKey('manufacture_year', $products['Clothes']);
		$this->assertArrayHasKey(0, $products['Clothes']);
		$this->assertArrayHasKey(1, $products['Clothes']);
		$this->assertArrayHasKey(2, $products['Clothes']);
		$this->assertArrayHasKey(3, $products['Clothes']);
		$this->assertArrayHasKey(4, $products['Clothes']);
		$this->assertArrayHasKey(5, $products['Clothes']);
		$this->assertArrayHasKey(6, $products['Clothes']);
		$this->assertArrayHasKey(7, $products['Clothes']);
		$this->assertArrayHasKey(8, $products['Clothes']);
	}
	
	public function testGroupedList() {
		//MSQLI_BOTH
		$products = $this->mapper->type('array<category>')->query("SELECT * FROM products ORDER BY product_id ASC");
		$this->assertInternalType('array', $products);
		$this->assertCount(3, $products);
		$this->assertArrayHasKey('Clothes', $products);
		$this->assertArrayHasKey('Hardware', $products);
		$this->assertArrayHasKey('Smartphones', $products);
	
		$this->assertInternalType('array', $products['Clothes']);
		$this->assertInternalType('array', $products['Hardware']);
		$this->assertInternalType('array', $products['Smartphones']);
	
		$this->assertCount(3, $products['Clothes']);
		$this->assertCount(1, $products['Hardware']);
		$this->assertCount(1, $products['Smartphones']);
	
		////
		$this->assertArrayHasKey(0, $products['Clothes']);
		$this->assertArrayHasKey(1, $products['Clothes']);
		$this->assertArrayHasKey(2, $products['Clothes']);
	
		$this->assertArrayHasKey('product_id', $products['Clothes'][0]);
		$this->assertInternalType('integer', $products['Clothes'][0]['product_id']);
		$this->assertEquals(1, $products['Clothes'][0]['product_id']);
	
		$this->assertArrayHasKey(0, $products['Clothes'][0]);
		$this->assertInternalType('integer', $products['Clothes'][0][0]);
		$this->assertEquals(1, $products['Clothes'][0][0]);
	
		$this->assertArrayHasKey('product_code', $products['Clothes'][0]);
		$this->assertInternalType('string', $products['Clothes'][0]['product_code']);
		$this->assertEquals('IND00054', $products['Clothes'][0]['product_code']);
	
		$this->assertArrayHasKey(1, $products['Clothes'][0]);
		$this->assertInternalType('string', $products['Clothes'][0][1]);
		$this->assertEquals('IND00054', $products['Clothes'][0][1]);
	
		$this->assertArrayHasKey('description', $products['Clothes'][0]);
		$this->assertInternalType('string', $products['Clothes'][0]['description']);
		$this->assertEquals('Red dress', $products['Clothes'][0]['description']);
	
		$this->assertArrayHasKey(2, $products['Clothes'][0]);
		$this->assertInternalType('string', $products['Clothes'][0][2]);
		$this->assertEquals('Red dress', $products['Clothes'][0][2]);
	
		$this->assertArrayHasKey('color', $products['Clothes'][0]);
		$this->assertInternalType('string', $products['Clothes'][0]['color']);
		$this->assertEquals('e11a1a', $products['Clothes'][0]['color']);
	
		$this->assertArrayHasKey(3, $products['Clothes'][0]);
		$this->assertInternalType('string', $products['Clothes'][0][3]);
		$this->assertEquals('e11a1a', $products['Clothes'][0][3]);
	
		$this->assertArrayHasKey('price', $products['Clothes'][0]);
		$this->assertInternalType('float', $products['Clothes'][0]['price']);
		$this->assertEquals(150.65, $products['Clothes'][0]['price']);
	
		$this->assertArrayHasKey(4, $products['Clothes'][0]);
		$this->assertInternalType('float', $products['Clothes'][0][4]);
		$this->assertEquals(150.65, $products['Clothes'][0][4]);
	
		$this->assertArrayHasKey('category', $products['Clothes'][0]);
		$this->assertInternalType('string', $products['Clothes'][0]['category']);
		$this->assertEquals('Clothes', $products['Clothes'][0]['category']);
	
		$this->assertArrayHasKey(5, $products['Clothes'][0]);
		$this->assertInternalType('string', $products['Clothes'][0][5]);
		$this->assertEquals('Clothes', $products['Clothes'][0][5]);
	
		$this->assertArrayHasKey('rating', $products['Clothes'][0]);
		$this->assertInternalType('float', $products['Clothes'][0]['rating']);
		$this->assertEquals(4.5, $products['Clothes'][0]['rating']);
	
		$this->assertArrayHasKey(6, $products['Clothes'][0]);
		$this->assertInternalType('float', $products['Clothes'][0][6]);
		$this->assertEquals(4.5, $products['Clothes'][0][6]);
	
		$this->assertArrayHasKey('refurbished', $products['Clothes'][0]);
		$this->assertInternalType('boolean', $products['Clothes'][0]['refurbished']);
		$this->assertFalse($products['Clothes'][0]['refurbished']);
	
		$this->assertArrayHasKey(7, $products['Clothes'][0]);
		$this->assertInternalType('boolean', $products['Clothes'][0][7]);
		$this->assertFalse($products['Clothes'][0][7]);
	
		$this->assertArrayHasKey('manufacture_year', $products['Clothes'][0]);
		$this->assertInternalType('integer', $products['Clothes'][0]['manufacture_year']);
		$this->assertEquals(2011, $products['Clothes'][0]['manufacture_year']);
	
		$this->assertArrayHasKey(8, $products['Clothes'][0]);
		$this->assertInternalType('integer', $products['Clothes'][0][8]);
		$this->assertEquals(2011, $products['Clothes'][0][8]);
	
		////
		$this->assertArrayHasKey('product_id', $products['Clothes'][1]);
		$this->assertInternalType('integer', $products['Clothes'][1]['product_id']);
		$this->assertEquals(2, $products['Clothes'][1]['product_id']);
	
		$this->assertArrayHasKey('product_code', $products['Clothes'][1]);
		$this->assertInternalType('string', $products['Clothes'][1]['product_code']);
		$this->assertEquals('IND00043', $products['Clothes'][1]['product_code']);
	
		$this->assertArrayHasKey('description', $products['Clothes'][1]);
		$this->assertInternalType('string', $products['Clothes'][1]['description']);
		$this->assertEquals('Blue jeans', $products['Clothes'][1]['description']);
	
		$this->assertArrayHasKey('color', $products['Clothes'][1]);
		$this->assertInternalType('string', $products['Clothes'][1]['color']);
		$this->assertEquals('0c1bd9', $products['Clothes'][1]['color']);
	
		$this->assertArrayHasKey('price', $products['Clothes'][1]);
		$this->assertInternalType('float', $products['Clothes'][1]['price']);
		$this->assertEquals(235.7, $products['Clothes'][1]['price']);
	
		$this->assertArrayHasKey('category', $products['Clothes'][1]);
		$this->assertInternalType('string', $products['Clothes'][1]['category']);
		$this->assertEquals('Clothes', $products['Clothes'][1]['category']);
	
		$this->assertArrayHasKey('rating', $products['Clothes'][1]);
		$this->assertInternalType('float', $products['Clothes'][1]['rating']);
		$this->assertEquals(3.9, $products['Clothes'][1]['rating']);
	
		$this->assertArrayHasKey('refurbished', $products['Clothes'][1]);
		$this->assertInternalType('boolean', $products['Clothes'][1]['refurbished']);
		$this->assertFalse($products['Clothes'][1]['refurbished']);
	
		$this->assertArrayHasKey('manufacture_year', $products['Clothes'][1]);
		$this->assertInternalType('integer', $products['Clothes'][1]['manufacture_year']);
		$this->assertEquals(2012, $products['Clothes'][1]['manufacture_year']);
	
		////
		$this->assertArrayHasKey('product_id', $products['Clothes'][2]);
		$this->assertInternalType('integer', $products['Clothes'][2]['product_id']);
		$this->assertEquals(3, $products['Clothes'][2]['product_id']);
	
		$this->assertArrayHasKey('product_code', $products['Clothes'][2]);
		$this->assertInternalType('string', $products['Clothes'][2]['product_code']);
		$this->assertEquals('IND00232', $products['Clothes'][2]['product_code']);
	
		$this->assertArrayHasKey('description', $products['Clothes'][2]);
		$this->assertInternalType('string', $products['Clothes'][2]['description']);
		$this->assertEquals('Green shirt', $products['Clothes'][2]['description']);
	
		$this->assertArrayHasKey('color', $products['Clothes'][2]);
		$this->assertInternalType('string', $products['Clothes'][2]['color']);
		$this->assertEquals('707c04', $products['Clothes'][2]['color']);
	
		$this->assertArrayHasKey('price', $products['Clothes'][2]);
		$this->assertInternalType('float', $products['Clothes'][2]['price']);
		$this->assertEquals(70.9, $products['Clothes'][2]['price']);
	
		$this->assertArrayHasKey('category', $products['Clothes'][2]);
		$this->assertInternalType('string', $products['Clothes'][2]['category']);
		$this->assertEquals('Clothes', $products['Clothes'][2]['category']);
	
		$this->assertArrayHasKey('rating', $products['Clothes'][2]);
		$this->assertInternalType('float', $products['Clothes'][2]['rating']);
		$this->assertEquals(4.1, $products['Clothes'][2]['rating']);
	
		$this->assertArrayHasKey('refurbished', $products['Clothes'][2]);
		$this->assertInternalType('boolean', $products['Clothes'][2]['refurbished']);
		$this->assertFalse($products['Clothes'][2]['refurbished']);
	
		$this->assertArrayHasKey('manufacture_year', $products['Clothes'][2]);
		$this->assertInternalType('integer', $products['Clothes'][2]['manufacture_year']);
		$this->assertEquals(2013, $products['Clothes'][2]['manufacture_year']);
	
		////
		$this->assertArrayHasKey(0, $products['Hardware']);
	
		////
		$this->assertArrayHasKey(0, $products['Smartphones']);
	
		//PGSQL_ASSOC
		$products = $this->mapper->type('array<category>', ArrayType::ASSOC)->query("SELECT * FROM products ORDER BY product_id ASC");
	
		$this->assertArrayHasKey('product_id', $products['Clothes'][0]);
		$this->assertArrayHasKey('product_code', $products['Clothes'][0]);
		$this->assertArrayHasKey('description', $products['Clothes'][0]);
		$this->assertArrayHasKey('color', $products['Clothes'][0]);
		$this->assertArrayHasKey('price', $products['Clothes'][0]);
		$this->assertArrayHasKey('category', $products['Clothes'][0]);
		$this->assertArrayHasKey('rating', $products['Clothes'][0]);
		$this->assertArrayHasKey('refurbished', $products['Clothes'][0]);
		$this->assertArrayHasKey('manufacture_year', $products['Clothes'][0]);
	
		$this->assertArrayNotHasKey(0, $products['Clothes'][0]);
		$this->assertArrayNotHasKey(1, $products['Clothes'][0]);
		$this->assertArrayNotHasKey(2, $products['Clothes'][0]);
		$this->assertArrayNotHasKey(3, $products['Clothes'][0]);
		$this->assertArrayNotHasKey(4, $products['Clothes'][0]);
		$this->assertArrayNotHasKey(5, $products['Clothes'][0]);
		$this->assertArrayNotHasKey(6, $products['Clothes'][0]);
		$this->assertArrayNotHasKey(7, $products['Clothes'][0]);
		$this->assertArrayNotHasKey(8, $products['Clothes'][0]);
	
		//PGSQL_NUM
		$products = $this->mapper->type('array<5>', ArrayType::NUM)->query("SELECT * FROM products ORDER BY product_id ASC");
	
		$this->assertArrayNotHasKey('product_id', $products['Clothes'][0]);
		$this->assertArrayNotHasKey('product_code', $products['Clothes'][0]);
		$this->assertArrayNotHasKey('description', $products['Clothes'][0]);
		$this->assertArrayNotHasKey('color', $products['Clothes'][0]);
		$this->assertArrayNotHasKey('price', $products['Clothes'][0]);
		$this->assertArrayNotHasKey('category', $products['Clothes'][0]);
		$this->assertArrayNotHasKey('rating', $products['Clothes'][0]);
		$this->assertArrayNotHasKey('refurbished', $products['Clothes'][0]);
		$this->assertArrayNotHasKey('manufacture_year', $products['Clothes'][0]);
	
		$this->assertArrayHasKey(0, $products['Clothes'][0]);
		$this->assertArrayHasKey(1, $products['Clothes'][0]);
		$this->assertArrayHasKey(2, $products['Clothes'][0]);
		$this->assertArrayHasKey(3, $products['Clothes'][0]);
		$this->assertArrayHasKey(4, $products['Clothes'][0]);
		$this->assertArrayHasKey(5, $products['Clothes'][0]);
		$this->assertArrayHasKey(6, $products['Clothes'][0]);
		$this->assertArrayHasKey(7, $products['Clothes'][0]);
		$this->assertArrayHasKey(8, $products['Clothes'][0]);
	}
}
?>