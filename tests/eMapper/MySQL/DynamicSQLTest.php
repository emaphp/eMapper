<?php
namespace eMapper\MySQL;

use eMapper\Engine\MySQL\Statement\MySQLStatement;
use Acme\Entity\Product;
use eMapper\Engine\MySQL\Type\MySQLTypeManager;

/**
 * Test dynamic sql expressions
 * 
 * @author emaphp
 * @group dynamic
 * @group mysql
 */
class DynamicSQLTest extends MySQLTest {
	public $statement;
	
	public function __construct() {
		self::setUpBeforeClass();
		$this->statement = new MySQLStatement(self::$conn, new MySQLTypeManager(), null);
	}
	
	public function testSimpleValue() {
		$result = $this->statement->build('[? null ?]', [], self::$env_config);
		$this->assertEquals('', $result);
		
		$result = $this->statement->build('[?string null ?]', [], self::$env_config);
		$this->assertEquals('NULL', $result);
		
		$result = $this->statement->build('[? 10 ?]', [], self::$env_config);
		$this->assertEquals('10', $result);
		
		$result = $this->statement->build('[?s 10 ?]', [], self::$env_config);
		$this->assertEquals("'10'", $result);
		
		$result = $this->statement->build('[?int 10 ?]', [], self::$env_config);
		$this->assertEquals(10, $result);
		
		$result = $this->statement->build('[? "test" ?]', [], self::$env_config);
		$this->assertEquals('test', $result);
		
		$result = $this->statement->build('[?string "test" ?]', [], self::$env_config);
		$this->assertEquals("'test'", $result);
		
		$result = $this->statement->build('[? (%0) ?]', [100], self::$env_config);
		$this->assertEquals('100', $result);
		
		$result = $this->statement->build('[?s (%0) ?]', [100], self::$env_config);
		$this->assertEquals("'100'", $result);
		
		$result = $this->statement->build('[? (%0) ?]', ["test"], self::$env_config);
		$this->assertEquals('test', $result);
		
		$result = $this->statement->build('[?s (%0) ?]', ["test"], self::$env_config);
		$this->assertEquals("'test'", $result);
		
		$result = $this->statement->build('[?i (%0) ?]', ["test"], self::$env_config);
		$this->assertEquals(0, $result);
	}
	
	public function testSimpleArgument() {
		$result = $this->statement->build('[? (if (int? (%0)) "%{i}" "%{s}") ?]', [25], self::$env_config);
		$this->assertEquals(25, $result);
		
		$result = $this->statement->build('[? (if (int? (%0)) "%{i}" "%{s}") ?]', ['joe'], self::$env_config);
		$this->assertEquals("'joe'", $result);
		
		$result = $this->statement->build('SELECT * FROM [? (if (int? (%0)) "user_id = %{i}") ?]', ['joe'], self::$env_config);
		$this->assertEquals('SELECT * FROM ', $result);
		
		$result = $this->statement->build('SELECT * FROM users WHERE [? (if (int? (%0)) "user_id = %{i}" "user_name = %{s}") ?]', [25], self::$env_config);
		$this->assertEquals("SELECT * FROM users WHERE user_id = 25", $result);
		
		$result = $this->statement->build('SELECT * FROM users WHERE [? (if (string? (%0)) "user_name = %{s}" "user_id = %{i}") ?]', ['joe'], self::$env_config);
		$this->assertEquals("SELECT * FROM users WHERE user_name = 'joe'", $result);
		
		$result = $this->statement->build('ORDER BY [? (. (%0) " " (strtoupper (%1))) ?]', ['name', 'desc'], self::$env_config);
		$this->assertEquals("ORDER BY name DESC", $result);
	}
	
	public function testComplexArgument() {
		//array
		$result = $this->statement->build('ORDER BY [? (. (if (#order_field?) (#order_field) "user_name") " " (if (#order_type?) (strtoupper (#order_type)) "DESC")) ?]', [['order_field' => 'user_id']], self::$env_config);
		$this->assertEquals("ORDER BY user_id DESC", $result);
		
		$result = $this->statement->build('ORDER BY [? (. (if (#order_field?) (#order_field) "user_name") " " (if (#order_type?) (strtoupper (#order_type)) "DESC")) ?]', [['order_field' => 'user_id', 'order_type' => 'asc']], self::$env_config);
		$this->assertEquals("ORDER BY user_id ASC", $result);
		
		//ArrayObject
		$result = $this->statement->build('ORDER BY [? (. (if (#order_field?) (#order_field) "user_name") " " (if (#order_type?) (strtoupper (#order_type)) "DESC")) ?]', [new \ArrayObject(['order_field' => 'user_id'])], self::$env_config);
		$this->assertEquals("ORDER BY user_id DESC", $result);
		
		$result = $this->statement->build('ORDER BY [? (. (if (#order_field?) (#order_field) "user_name") " " (if (#order_type?) (strtoupper (#order_type)) "DESC")) ?]', [new \ArrayObject(['order_field' => 'user_id', 'order_type' => 'asc'])], self::$env_config);
		$this->assertEquals("ORDER BY user_id ASC", $result);
		
		//stdClass
		$order = new \stdClass();
		$order->field = 'user_id';
		$result = $this->statement->build('ORDER BY [? (. (if (#field?) (#field) "user_name") " " (if (#type?) (strtoupper (#type)) "DESC")) ?]', [$order], self::$env_config);
		$this->assertEquals("ORDER BY user_id DESC", $result);
		
		$order->type = 'asc';
		$result = $this->statement->build('ORDER BY [? (. (if (#field?) (#field) "user_name") " " (if (#type?) (strtoupper (#type)) "DESC")) ?]', [$order], self::$env_config);
		$this->assertEquals("ORDER BY user_id ASC", $result);
		
		//entity
		$product = new Product();
		$product->code = 'ZXY321';
		$product->setCategory('Clothes');
		
		$result = $this->statement->build('WHERE [? (if (#code?) "product_code = #{code}" "1") ?]', [$product], self::$env_config);
		$this->assertEquals("WHERE product_code = 'ZXY321'", $result);
		
		$result = $this->statement->build('WHERE [? (if (#category?) "category = #{category}" "1") ?]', [$product], self::$env_config);
		$this->assertEquals("WHERE category = 'Clothes'", $result);
	}
	
	public function testConfigurationMacros() {
		self::$mapper->setOption('order.column', 'user_id');
		$this->assertEquals('user_id', self::$mapper->getOption('order.column'));
		$query = "SELECT user_id FROM users ORDER BY [? (@order.column) ?] [? (if (@order.type?) (@order.type) 'ASC') ?]";
		
		$id = self::$mapper->type('i')->query($query);
		$this->assertEquals(1, $id);
		
		self::$mapper->setOption('order.type', 'DESC');
		$id = self::$mapper->type('i')->query($query);
		$this->assertEquals(5, $id);
		
		$id = self::$mapper->type('i')->option('order.column', 'user_name')->query($query);
		$this->assertEquals(2, $id);
	}
	
	public function testTypifiedExpression() {
		$query = "SELECT * FROM products WHERE color = [?Acme\RGBColor (new Acme\RGBColor 112 124 4) ?]";
		$products = self::$mapper->type('obj[]')->query($query);
		$this->assertCount(1, $products);
		$this->assertEquals(3, $products[0]->product_id);
	}
	
	/**
	 * @expectedException eMapper\Engine\MySQL\Exception\MySQLException
	 */
	public function testEvaluationOrder() {
		$dt = new \DateTime('2013-05-22');
		$query = "SELECT * FROM users [? (if (%0?) 'WHERE last_login > [?date (%0) ?]' ) ?]";
		$users = self::$mapper->type('obj[user_id]')
		->query_override(function($query) {
			$this->assertEquals("SELECT * FROM users WHERE last_login > '2013-05-22'", $query);
		})
		->query($query, $dt);
		$this->assertCount(2, $users);
		$this->assertArrayHasKey(1, $users);
		$this->assertArrayHasKey(5, $users);
	}
}
?>