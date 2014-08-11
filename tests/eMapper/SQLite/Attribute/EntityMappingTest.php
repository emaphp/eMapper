<?php
namespace eMapper\SQLite\Attribute;

use eMapper\SQLite\SQLiteConfig;
use eMapper\Attribute\AbstractEntityMappingTest;

/**
 * Test dynamic attributes on entities
 * @author emaphp
 * @group sqlite
 * @group attribute
 */
class EntityMappingTest extends AbstractEntityMappingTest {
	use SQLiteConfig;
	
	public function testStatementAttribute() {
		$sale = $this->mapper->type('obj:Acme\Result\Attribute\ExtraSale')->query("SELECT * FROM sales WHERE sale_id = 3");
		$this->assertInstanceOf('Acme\Result\Attribute\ExtraSale', $sale);
	
		//productId
		$this->assertInternalType('integer', $sale->productId);
		$this->assertEquals(4, $sale->productId);
	
		//userId
		$this->assertInternalType('integer', $sale->userId);
		$this->assertEquals(2, $sale->userId);
	
		//product
		$this->assertInternalType('array', $sale->product);
		$this->assertEquals(4, $sale->product['product_id']);
		$this->assertEquals('GFX00067', $sale->product['product_code']);
		$this->assertEquals('ATI HD 9999', $sale->product['description']);
		$this->assertEquals(null, $sale->product['color']);
		$this->assertEquals(120.75, $sale->product['price']);
		$this->assertEquals('Hardware', $sale->product['category']);
		$this->assertEquals(3.8, $sale->product['rating']);
		$this->assertEquals(0, $sale->product['refurbished']);
		$this->assertEquals(2013, $sale->product['manufacture_year']);
	
		//user
		$this->assertInternalType('object', $sale->user);
		$this->assertInstanceOf('stdClass', $sale->user);
		$this->assertEquals(2, $sale->user->user_id);
		$this->assertEquals('okenobi', $sale->user->user_name);
		$this->assertEquals('1976-03-03', $sale->user->birth_date);
		$this->assertEquals('2013-01-06 12:34:10', $sale->user->last_login);
		$this->assertEquals('00:00:00', $sale->user->newsletter_time);
		$this->assertEquals($this->getBlob(), $sale->user->avatar);
	}
}
?>