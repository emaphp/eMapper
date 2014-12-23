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
		$sale = $this->mapper
		->type('obj:Acme\Result\Attribute\SQLiteSale')
		->query("SELECT * FROM sales WHERE sale_id = 3");
		$this->assertInstanceOf('Acme\Result\Attribute\SQLiteSale', $sale);
	
		//productId
		$this->assertInternalType('integer', $sale->productId);
		$this->assertEquals(4, $sale->productId);
	
		//userId
		$this->assertInternalType('integer', $sale->userId);
		$this->assertEquals(2, $sale->userId);
		
		//user
		$this->assertInternalType('object', $sale->user);
		$this->assertInstanceOf('stdClass', $sale->user);
		
		$this->assertObjectHasAttribute('id', $sale->user);
		$this->assertEquals(2, $sale->user->id);
		$this->assertObjectHasAttribute('birthDate', $sale->user);
		$this->assertInstanceOf('DateTime', $sale->user->birthDate);
		$this->assertEquals('1976-03-03', $sale->user->birthDate->format('Y-m-d'));
		
		$this->assertObjectHasAttribute('uppercase_name', $sale->user);
		$this->assertObjectHasAttribute('fakeId', $sale->user);
		$this->assertObjectHasAttribute('age', $sale->user);
	}
}
?>