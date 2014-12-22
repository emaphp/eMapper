<?php
namespace eMapper\Dynamic;

use eMapper\StatementTest;
use Acme\Entity\Product;

abstract class AbstractDynamicSQLStatementTest extends StatementTest {
	public static $env_config = ['env.id' => 'default', 'env.class' => 'eMapper\Dynamic\Environment\DynamicSQLEnvironment'];
	
	public function testSimpleValue() {
		$result = $this->statement->format('[? null ?]', [], self::$env_config);
		$this->assertEquals('', $result);
	
		$result = $this->statement->format('[?string null ?]', [], self::$env_config);
		$this->assertEquals('NULL', $result);
	
		$result = $this->statement->format('[? 10 ?]', [], self::$env_config);
		$this->assertEquals('10', $result);
	
		$result = $this->statement->format('[?string 10 ?]', [], self::$env_config);
		$this->assertEquals("'10'", $result);
	
		$result = $this->statement->format('[?int 10 ?]', [], self::$env_config);
		$this->assertEquals(10, $result);
	
		$result = $this->statement->format('[? "test" ?]', [], self::$env_config);
		$this->assertEquals('test', $result);
	
		$result = $this->statement->format('[?string "test" ?]', [], self::$env_config);
		$this->assertEquals("'test'", $result);
	
		$result = $this->statement->format('[? (%0) ?]', [100], self::$env_config);
		$this->assertEquals('100', $result);
	
		$result = $this->statement->format('[?s (%0) ?]', [100], self::$env_config);
		$this->assertEquals("'100'", $result);
	
		$result = $this->statement->format('[? (%0) ?]', ["test"], self::$env_config);
		$this->assertEquals('test', $result);
	
		$result = $this->statement->format('[?str (%0) ?]', ["test"], self::$env_config);
		$this->assertEquals("'test'", $result);
	
		$result = $this->statement->format('[?int (%0) ?]', ["test"], self::$env_config);
		$this->assertEquals(0, $result);
	}
	
	public function testSimpleArgument() {
		$result = $this->statement->format('[? (if (int? (%0)) "%{i}" "%{s}") ?]', [25], self::$env_config);
		$this->assertEquals(25, $result);
	
		$result = $this->statement->format('[? (if (int? (%0)) "%{i}" "%{s}") ?]', ['joe'], self::$env_config);
		$this->assertEquals("'joe'", $result);
	
		$result = $this->statement->format('SELECT * FROM [? (if (int? (%0)) "user_id = %{i}") ?]', ['joe'], self::$env_config);
		$this->assertEquals('SELECT * FROM ', $result);
	
		$result = $this->statement->format('SELECT * FROM users WHERE [? (if (int? (%0)) "user_id = %{i}" "user_name = %{s}") ?]', [25], self::$env_config);
		$this->assertEquals("SELECT * FROM users WHERE user_id = 25", $result);
	
		$result = $this->statement->format('SELECT * FROM users WHERE [? (if (string? (%0)) "user_name = %{s}" "user_id = %{i}") ?]', ['joe'], self::$env_config);
		$this->assertEquals("SELECT * FROM users WHERE user_name = 'joe'", $result);
	
		$result = $this->statement->format('ORDER BY [? (. (%0) " " (strtoupper (%1))) ?]', ['name', 'desc'], self::$env_config);
		$this->assertEquals("ORDER BY name DESC", $result);
	}
	
	public function testComplexArgument() {
		//array
		$result = $this->statement->format('ORDER BY [? (. (if (#order_field?) (#order_field) "user_name") " " (if (#order_type?) (strtoupper (#order_type)) "DESC")) ?]', [['order_field' => 'user_id']], self::$env_config);
		$this->assertEquals("ORDER BY user_id DESC", $result);
	
		$result = $this->statement->format('ORDER BY [? (. (if (#order_field?) (#order_field) "user_name") " " (if (#order_type?) (strtoupper (#order_type)) "DESC")) ?]', [['order_field' => 'user_id', 'order_type' => 'asc']], self::$env_config);
		$this->assertEquals("ORDER BY user_id ASC", $result);
	
		//ArrayObject
		$result = $this->statement->format('ORDER BY [? (. (if (#order_field?) (#order_field) "user_name") " " (if (#order_type?) (strtoupper (#order_type)) "DESC")) ?]', [new \ArrayObject(['order_field' => 'user_id'])], self::$env_config);
		$this->assertEquals("ORDER BY user_id DESC", $result);
	
		$result = $this->statement->format('ORDER BY [? (. (if (#order_field?) (#order_field) "user_name") " " (if (#order_type?) (strtoupper (#order_type)) "DESC")) ?]', [new \ArrayObject(['order_field' => 'user_id', 'order_type' => 'asc'])], self::$env_config);
		$this->assertEquals("ORDER BY user_id ASC", $result);
	
		//stdClass
		$order = new \stdClass();
		$order->field = 'user_id';
		$result = $this->statement->format('ORDER BY [? (. (if (#field?) (#field) "user_name") " " (if (#type?) (strtoupper (#type)) "DESC")) ?]', [$order], self::$env_config);
		$this->assertEquals("ORDER BY user_id DESC", $result);
	
		$order->type = 'asc';
		$result = $this->statement->format('ORDER BY [? (. (if (#field?) (#field) "user_name") " " (if (#type?) (strtoupper (#type)) "DESC")) ?]', [$order], self::$env_config);
		$this->assertEquals("ORDER BY user_id ASC", $result);
	
		//entity
		$product = new Product();
		$product->code = 'ZXY321';
		$product->setCategory('Clothes');
	
		$result = $this->statement->format('WHERE [? (if (#code?) "product_code = #{code}" "1") ?]', [$product], self::$env_config);
		$this->assertEquals("WHERE product_code = 'ZXY321'", $result);
	
		$result = $this->statement->format('WHERE [? (if (#category?) "category = #{category}" "1") ?]', [$product], self::$env_config);
		$this->assertEquals("WHERE category = 'Clothes'", $result);
	}
}