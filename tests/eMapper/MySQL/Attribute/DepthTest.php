<?php
namespace eMapper\MySQL\Attribute;

use eMapper\MySQL\MySQLTest;
use eMapper\Engine\MySQL\MySQLMapper;
use eMapper\SQL\Statement;

/**
 * Test setting different values for depth internal value
 * 
 * @author emaphp
 * @group mysql
 * @group depth
 */
class DepthTest extends MySQLTest {
	public $xmapper;
	
	public function __construct() {
		parent::__construct();
		
		//create specialized mapper
		$this->xmapper = new MySQLMapper(new \mysqli(self::$config['host'], self::$config['user'], self::$config['password'], self::$config['database']));
		
		$this->xmapper->stmt('findBoughtProducts',
							"SELECT p.product_id, p.product_code, p.category, p.price
							 FROM sales s INNER JOIN products p ON s.product_id = p.product_id
							 WHERE s.user_id = %{i}",
							 Statement::type('obj:Acme\Depth\Product[]'));
		
		$this->xmapper->stmt('totalBoughtProducts',
							"SELECT COUNT(*)
							 FROM sales s INNER JOIN products p ON s.product_id = p.product_id
							 WHERE s.user_id = %{i}",
							Statement::type('int'));
		
		$this->xmapper->stmt('findRelatedProducts',
							"SELECT * FROM products
							 WHERE category = %{s} AND product_id <> %{i}
							 ORDER BY product_id ASC",
							Statement::type('obj:Acme\Depth\Product[id]'));
	}
	
	public function testNoDepth() {
		$user = $this->xmapper->type('obj:Acme\Depth\User')
		->depth(0)
		->query("SELECT * FROM users WHERE user_id = 5");
		
		$this->assertInstanceOf('Acme\Depth\User', $user);
		$this->assertEquals(5, $user->id);
		$this->assertEquals('ishmael', $user->name);
		$this->assertNull($user->products);
		$this->assertEquals(1, $user->totalProducts);
	}
	
	public function testDefaultDepth() {
		$user = $this->xmapper->type('obj:Acme\Depth\User')
		->query("SELECT * FROM users WHERE user_id = 5");
		
		$this->assertInstanceOf('Acme\Depth\User', $user);
		$this->assertEquals(5, $user->id);
		$this->assertEquals('ishmael', $user->name);
		$this->assertEquals(1, $user->totalProducts);
		
		$this->assertInternalType('array', $user->products);
		$this->assertCount(1, $user->products);
		
		$product = $user->products[0];
		$this->assertInstanceOf('Acme\Depth\Product', $product);
		$this->assertEquals(2, $product->id);
		$this->assertEquals('IND00043', $product->code);
		$this->assertEquals('Clothes', $product->category);
		$this->assertEquals(235.7, $product->price);
		$this->assertNull($product->related);
	}
	
	public function testExtendedDepth() {
		$user = $this->xmapper->type('obj:Acme\Depth\User')
		->depth(2)
		->query("SELECT * FROM users WHERE user_id = 5");
		
		$this->assertInstanceOf('Acme\Depth\User', $user);
		$this->assertEquals(5, $user->id);
		$this->assertEquals('ishmael', $user->name);
		$this->assertEquals(1, $user->totalProducts);
		
		$this->assertInternalType('array', $user->products);
		$this->assertCount(1, $user->products);
		
		$product = $user->products[0];
		$this->assertInstanceOf('Acme\Depth\Product', $product);
		$this->assertEquals(2, $product->id);
		$this->assertEquals('IND00043', $product->code);
		$this->assertEquals('Clothes', $product->category);
		$this->assertEquals(235.7, $product->price);
		
		$this->assertInternalType('array', $product->related);
		
		$this->assertArrayHasKey(1, $product->related);
		$this->assertInstanceOf('Acme\Depth\Product', $product->related[1]);
		$this->assertEquals(1, $product->related[1]->id);
		$this->assertEquals('IND00054', $product->related[1]->code);
		$this->assertEquals('Clothes', $product->related[1]->category);
		$this->assertEquals(150.65, $product->related[1]->price);
		
		$this->assertArrayHasKey(3, $product->related);
		$this->assertInstanceOf('Acme\Depth\Product', $product->related[3]);
		$this->assertEquals(3, $product->related[3]->id);
		$this->assertEquals('IND00232', $product->related[3]->code);
		$this->assertEquals('Clothes', $product->related[3]->category);
		$this->assertEquals(70.9, $product->related[3]->price);
	}
}
?>