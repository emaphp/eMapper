<?php
namespace eMapper\SQLite;

use eMapper\AbstractEntityNamespaceTest;
use eMapper\Engine\SQLite\SQLiteDriver;
use eMapper\Mapper;
use Acme\Type\RGBColorTypeHandler;

/**
 * 
 * @author emaphp
 * @group namespace
 */
class EntityNamespaceTest extends AbstractEntityNamespaceTest {
	public function build() {
		$this->driver = new SQLiteDriver(SQLiteTest::$filename);
		$this->mapper = new Mapper($this->driver);
		$this->mapper->addType('Acme\RGBColor', new RGBColorTypeHandler());
	}
	
	public function testMatches() {
		$products = $this->mapper->execute('products.codeMatches', '9');
		$this->assertCount(1, $products);
	
		$products = $this->mapper->execute('products.codeMatches', '^IND');
		$this->assertCount(3, $products);
	
		$products = $this->mapper->execute('products.codeMatches', '^i');
		$this->assertCount(0, $products);
	}
	
	public function testNotMatches() {
		$products = $this->mapper->execute('products.codeNotMatches', '9');
		$this->assertCount(4, $products);
	
		$products = $this->mapper->execute('products.codeNotMatches', '^IND');
		$this->assertCount(2, $products);
	
		$products = $this->mapper->execute('products.codeNotMatches', '^i');
		$this->assertCount(5, $products);
	}
	
	public function testIMatches() {
		$products = $this->mapper->execute('products.codeIMatches', '^IND');
		$this->assertCount(3, $products);
	
		$products = $this->mapper->execute('products.codeIMatches', '^i');
		$this->assertCount(3, $products);
	}
	
	public function testNotIMatches() {
		$products = $this->mapper->execute('products.codeNotIMatches', '^IND');
		$this->assertCount(2, $products);
	
		$products = $this->mapper->execute('products.codeNotIMatches', '^i');
		$this->assertCount(2, $products);
	}
}
?>