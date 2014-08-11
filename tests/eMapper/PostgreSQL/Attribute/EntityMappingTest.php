<?php
namespace eMapper\PostgreSQL\Attribute;

use eMapper\PostgreSQL\PostgreSQLConfig;
use eMapper\Attribute\AbstractEntityMappingTest;

/**
 * Test dynamic attributes on entities
 *
 * @author emaphp
 * @group attribute
 * @group postgre
 */
class EntityMappingTest extends AbstractEntityMappingTest {
	use PostgreSQLConfig;
	
	public function testProcedureAttribute() {
		$product = $this->mapper->type('obj:Acme\Result\Attribute\Product')->query("SELECT * FROM products WHERE product_id = 3");
		$this->assertInstanceOf('Acme\Result\Attribute\Product', $product);
	
		//id
		$this->assertEquals(3, $product->id);
	
		//category
		$this->assertEquals('Clothes', $product->category);
	
		//color
		$this->assertInstanceOf('Acme\RGBColor', $product->color);
	
		//lastSale
		$sale = $product->lastSale;
		$this->assertInstanceOf('Acme\Result\Attribute\Sale', $sale);
		$this->assertEquals(3, $sale->productId);
		$this->assertEquals(3, $sale->userId);
		$this->assertNull($sale->product);
		$this->assertNull($sale->user);
	
		//bestInCategory
		$best = $product->bestInCategory;
		$this->assertInstanceOf('Acme\Result\Attribute\Product', $best);
		$this->assertEquals(1, $best->id);
		$this->assertEquals('Clothes', $best->category);
		$this->assertInstanceOf('Acme\RGBColor', $best->color);
		$this->assertNull($best->lastSale);
		$this->assertNull($best->bestInCategory);
		$this->assertEquals(152.41666666667, $best->avgPrice);
	
		//avgPrice
		$avg = $product->avgPrice;
		$this->assertEquals(152, intval($avg));
	}
}
?>