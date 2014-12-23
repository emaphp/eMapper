<?php
namespace eMapper\Fluent;

use eMapper\MapperTest;
use eMapper\Query\Column;

abstract class AbstractFluentUpdateTest extends MapperTest {
	public function testSet() {
		$query = $this->mapper->newQuery();
		list($sql, $args) = $query->update('users')
		->set('name', 'emaphp')->build();
		$this->assertEquals('UPDATE users SET name=#{name}', $sql);
		$this->assertInternalType('array', $args);
		$this->assertCount(1, $args);
		$this->assertInternalType('array', $args[0]);
		$this->assertArrayHasKey('name', $args[0]);
		$this->assertEquals('emaphp', $args[0]['name']);
		
		$query = $this->mapper->newQuery();
		list($sql, $args) = $query->update('users', 'u')
		->set('name', 'emaphp')
		->set('role', 'developer')
		->build();
		$this->assertEquals('UPDATE users u SET name=#{name},role=#{role}', $sql);
		$this->assertInternalType('array', $args);
		$this->assertCount(1, $args);
		$this->assertInternalType('array', $args[0]);
		$this->assertArrayHasKey('name', $args[0]);
		$this->assertEquals('emaphp', $args[0]['name']);
		$this->assertArrayHasKey('role', $args[0]);
		$this->assertEquals('developer', $args[0]['role']);
		
		$query = $this->mapper->newQuery();
		list($sql, $args) = $query->update('users')
		->setExpr('name = %{s}, role = %{s}', 'emaphp', 'developer')
		->build();
		
		$this->assertEquals('UPDATE users SET name = %{s}, role = %{s}', $sql);
		$this->assertInternalType('array', $args);
		$this->assertCount(2, $args);
		$this->assertEquals('emaphp', $args[0]);
		$this->assertEquals('developer', $args[1]);
		
		$query = $this->mapper->newQuery();
		list($sql, $args) = $query->update('users')
		->setValue(['name' => 'emaphp', 'role' => 'developer'])
		->build();
		$this->assertEquals('UPDATE users SET name=#{name},role=#{role}', $sql);
		$this->assertInternalType('array', $args);
		$this->assertCount(1, $args);
		$this->assertInternalType('array', $args[0]);
		$this->assertArrayHasKey('name', $args[0]);
		$this->assertEquals('emaphp', $args[0]['name']);
	}
	
	public function testWhere() {
		$query = $this->mapper->newQuery();
		list($sql, $args) = $query->update('users', 'u')
		->set('name', 'emaphp')
		->set('role', 'developer')
		->where('id = 1')
		->build();
		$this->assertEquals('UPDATE users u SET name=#{name},role=#{role} WHERE id = 1', $sql);
		$this->assertInternalType('array', $args);
		$this->assertCount(1, $args);
		$this->assertInternalType('array', $args[0]);
		$this->assertArrayHasKey('name', $args[0]);
		$this->assertEquals('emaphp', $args[0]['name']);
		$this->assertArrayHasKey('role', $args[0]);
		$this->assertEquals('developer', $args[0]['role']);
		
		$query = $this->mapper->newQuery();
		list($sql, $args) = $query->update('users', 'u')
		->set('name', 'emaphp')
		->set('role', 'developer')
		->where(Column::id()->eq(1))
		->build();
		
		$this->assertRegExp('/UPDATE users u SET name=#\{name\},role=#\{role\} WHERE id = #\{\$\d+\}/', $sql);
		preg_match('@\{\$(\w+)\}@', $sql, $matches);
		$key = '$' . $matches[1];
		$this->assertCount(1, $args);
		$this->assertInternalType('array', $args[0]);
		$this->assertArrayHasKey('name', $args[0]);
		$this->assertEquals('emaphp', $args[0]['name']);
		$this->assertArrayHasKey('role', $args[0]);
		$this->assertEquals('developer', $args[0]['role']);
		$this->assertArrayHasKey($key, $args[0]);
		$this->assertEquals(1, $args[0][$key]);
		
		$query = $this->mapper->newQuery();
		list($sql, $args) = $query->update('users', 'u')
		->set('name', 'emaphp')
		->set('role', 'developer')
		->where('id = %{i} OR role = %{s}', 1, 'manager')
		->build();
		$this->assertEquals('UPDATE users u SET name=#{name},role=#{role} WHERE id = %{i} OR role = %{s}', $sql);
		$this->assertInternalType('array', $args);
		$this->assertCount(3, $args);
		$this->assertInternalType('array', $args[0]);
		$this->assertArrayHasKey('name', $args[0]);
		$this->assertEquals('emaphp', $args[0]['name']);
		$this->assertArrayHasKey('role', $args[0]);
		$this->assertEquals('developer', $args[0]['role']);
		$this->assertEquals(1, $args[1]);
		$this->assertEquals('manager', $args[2]);
		
		////
		$query = $this->mapper->newQuery();
		list($sql, $args) = $query->update('users')
		->setExpr('name = %{s}, role = %{s}', 'emaphp', 'developer')
		->where(Column::id()->eq(1))
		->build();
		
		$this->assertRegExp('/UPDATE users SET name = %\{s\}, role = %\{s\} WHERE id = #\{\$\d+\}/', $sql);
		preg_match('@\{\$(\w+)\}@', $sql, $matches);
		$key = '$' . $matches[1];
		
		$this->assertInternalType('array', $args);
		$this->assertCount(3, $args);
		$this->assertInternalType('array', $args[0]);
		$this->assertArrayHasKey($key, $args[0]);
		$this->assertEquals(1, $args[0][$key]);
		$this->assertEquals('emaphp', $args[1]);
		$this->assertEquals('developer', $args[2]);
		
		////
		$query = $this->mapper->newQuery();
		list($sql, $args) = $query->update('users')
		->setExpr('name = %{s}, role = %{s}', 'emaphp', 'developer')
		->where('id = %{i}', 1)
		->build();
		
		$this->assertEquals('UPDATE users SET name = %{s}, role = %{s} WHERE id = %{i}', $sql);
		$this->assertCount(3, $args);
		$this->assertEquals('emaphp', $args[0]);
		$this->assertEquals('developer', $args[1]);
		$this->assertEquals(1, $args[2]);
		
		//
		$query = $this->mapper->newQuery();
		list($sql, $args) = $query->update('users')
		->setValue(['name' => 'emaphp', 'role' => 'developer'])
		->where(Column::id()->eq(1))
		->build();
		$this->assertRegExp('/UPDATE users SET name=#\{name\},role=#\{role\} WHERE id = #\{\$\w+\}/', $sql);
		$this->assertInternalType('array', $args);
		$this->assertCount(1, $args);
		preg_match('@\{\$(\w+)\}@', $sql, $matches);
		$key = '$' . $matches[1];
		$this->assertInternalType('array', $args[0]);
		$this->assertArrayHasKey('name', $args[0]);
		$this->assertEquals('emaphp', $args[0]['name']);
		$this->assertArrayHasKey('role', $args[0]);
		$this->assertEquals('developer', $args[0]['role']);
		$this->assertArrayHasKey($key, $args[0]);
		$this->assertEquals(1, $args[0][$key]);
		
		//
		$query = $this->mapper->newQuery();
		list($sql, $args) = $query->update('users')
		->setValue(['name' => 'emaphp', 'role' => 'developer'])
		->where('id = %{i}', 1)
		->build();
		$this->assertEquals('UPDATE users SET name=#{name},role=#{role} WHERE id = %{i}', $sql);
		$this->assertCount(2, $args);
		$this->assertInternalType('array', $args[0]);
		$this->assertArrayHasKey('name', $args[0]);
		$this->assertEquals('emaphp', $args[0]['name']);
		$this->assertArrayHasKey('role', $args[0]);
		$this->assertEquals('developer', $args[0]['role']);
		$this->assertEquals(1, $args[1]);
	}
}
?>
