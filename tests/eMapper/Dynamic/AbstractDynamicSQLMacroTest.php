<?php
namespace eMapper\Dynamic;

use eMapper\MapperTest;

abstract class AbstractDynamicSQLMacroTest extends MapperTest {
	public function testConfigurationMacros() {
		$this->mapper->setOption('order.column', 'user_id');
		$this->assertEquals('user_id', $this->mapper->getOption('order.column'));
		$query = "SELECT user_id FROM users ORDER BY [? (@order.column) ?] [? (if (@order.type?) (@order.type) 'ASC') ?]";
	
		$id = $this->mapper->type('i')->query($query);
		$this->assertEquals(1, $id);
	
		$this->mapper->setOption('order.type', 'DESC');
		$id = $this->mapper->type('i')->query($query);
		$this->assertEquals(5, $id);
	
		$id = $this->mapper->type('i')->option('order.column', 'user_name')->query($query);
		$this->assertEquals(2, $id);
	}
	
	public function testTypifiedExpression() {
		$query = "SELECT * FROM products WHERE color = [?Acme\RGBColor (new Acme\RGBColor 112 124 4) ?]";
		$products = $this->mapper->type('obj[]')->query($query);
		$this->assertCount(1, $products);
		$this->assertEquals(3, $products[0]->product_id);
	}
	
	/**
	 * @expectedException eMacros\Exception\ParseException
	 */
	public function testEvaluationOrder() {
		$dt = new \DateTime('2013-05-22');
		$query = "SELECT * FROM users [? (if (%0?) 'WHERE last_login > [?date (%0) ?]' ) ?]";
		$users = $this->mapper->type('obj[user_id]')
		->debug(function($query) {
			$this->assertEquals("SELECT * FROM users WHERE last_login > '2013-05-22'", $query);
		})
		->query($query, $dt);
		$this->assertCount(2, $users);
		$this->assertArrayHasKey(1, $users);
		$this->assertArrayHasKey(5, $users);
	}
}
