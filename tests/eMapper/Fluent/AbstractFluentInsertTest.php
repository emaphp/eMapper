<?php
namespace eMapper\Fluent;

use eMapper\MapperTest;
use eMapper\Query\Column;

abstract class AbstractFluentInsertTest extends MapperTest {
	public function testValues() {
		$query = $this->mapper->newQuery();
		list($sql, $args) = $query->insertInto('users')->values('emaphp', 'developer')->build();
		$this->assertEquals("INSERT INTO @@users VALUES (%{0},%{1})", $sql);
		$this->assertCount(2, $args);
		$this->assertEquals('emaphp', $args[0]);
		$this->assertEquals('developer', $args[1]);
		
		$query = $this->mapper->newQuery();
		list($sql, $args) = $query->insertInto('users')->valuesExpr('%{s}, %{i}', 'emaphp', 1000)->build();
		$this->assertEquals("INSERT INTO @@users VALUES (%{s}, %{i})", $sql);
		$this->assertCount(2, $args);
		$this->assertEquals('emaphp', $args[0]);
		$this->assertEquals(1000, $args[1]);
	}
	
	public function testColumns() {
		$query = $this->mapper->newQuery();
		list($sql, $args) = $query->insertInto('users')->columns('username', 'role')->values('emaphp', 'developer')->build();
		$this->assertEquals("INSERT INTO @@users (username,role) VALUES (%{0},%{1})", $sql);
		$this->assertCount(2, $args);
		$this->assertEquals('emaphp', $args[0]);
		$this->assertEquals('developer', $args[1]);
		
		$query = $this->mapper->newQuery();
		list($sql, $args) = $query->insertInto('users')->columns('name','salary')->valuesExpr('%{s}, %{i}', 'emaphp', 1000)->build();
		$this->assertEquals("INSERT INTO @@users (name,salary) VALUES (%{s}, %{i})", $sql);
		$this->assertCount(2, $args);
		$this->assertEquals('emaphp', $args[0]);
		$this->assertEquals(1000, $args[1]);
		
		$query = $this->mapper->newQuery();
		list($sql, $args) = $query->insertInto('users')->columns(Column::name(), Column::salary())->valuesExpr('%{s}, %{i}', 'emaphp', 1000)->build();
		$this->assertEquals("INSERT INTO @@users (name,salary) VALUES (%{s}, %{i})", $sql);
		$this->assertCount(2, $args);
		$this->assertEquals('emaphp', $args[0]);
		$this->assertEquals(1000, $args[1]);
	}
	
	public function testValuesArray() {
		$query = $this->mapper->newQuery();
		list($sql, $args) = $query->insertInto('users')->valuesArray(['emaphp', 'developer'])->build();
		$this->assertEquals("INSERT INTO @@users VALUES (%{0},%{1})", $sql);
		$this->assertCount(2, $args);
		$this->assertEquals('emaphp', $args[0]);
		$this->assertEquals('developer', $args[1]);
		
		$query = $this->mapper->newQuery();
		list($sql, $args) = $query->insertInto('users')->columns('username', 'role')->valuesArray(['emaphp', 'developer'])->build();
		$this->assertEquals("INSERT INTO @@users (username,role) VALUES (%{0},%{1})", $sql);
		$this->assertCount(2, $args);
		$this->assertEquals('emaphp', $args[0]);
		$this->assertEquals('developer', $args[1]);
		
		$query = $this->mapper->newQuery();
		list($sql, $args) = $query->insertInto('users')->valuesArray(['name' => 'emaphp', 'role' => 'developer'])->build();
		$this->assertEquals("INSERT INTO @@users (name,role) VALUES (#{name},#{role})", $sql);
		$this->assertCount(1, $args);
		$this->assertEquals('emaphp', $args[0]['name']);
		$this->assertEquals('developer', $args[0]['role']);
		
		$query = $this->mapper->newQuery();
		list($sql, $args) = $query->insertInto('users')
		->columns('role', 'name')
		->valuesArray(['name' => 'emaphp', 'role' => 'developer'])->build();
		$this->assertEquals("INSERT INTO @@users (role,name) VALUES (#{role},#{name})", $sql);
		$this->assertCount(1, $args);
		$this->assertEquals('emaphp', $args[0]['name']);
		$this->assertEquals('developer', $args[0]['role']);
		
		$query = $this->mapper->newQuery();
		list($sql, $args) = $query->insertInto('users')
		->columns('role', 'name:s')
		->valuesArray(['name' => 'emaphp', 'role' => 'developer'])->build();
		$this->assertEquals("INSERT INTO @@users (role,name) VALUES (#{role},#{name:s})", $sql);
		$this->assertCount(1, $args);
		$this->assertEquals('emaphp', $args[0]['name']);
		$this->assertEquals('developer', $args[0]['role']);
		
		$query = $this->mapper->newQuery();
		list($sql, $args) = $query->insertInto('users')
		->columns(Column::role('string'), Column::name())
		->valuesArray(['name' => 'emaphp', 'role' => 'developer'])->build();
		$this->assertEquals("INSERT INTO @@users (role,name) VALUES (#{role:string},#{name})", $sql);
		$this->assertCount(1, $args);
		$this->assertEquals('emaphp', $args[0]['name']);
		$this->assertEquals('developer', $args[0]['role']);
		
		$query = $this->mapper->newQuery();
		list($sql, $args) = $query->insertInto('users')->valuesExpr('#{name:s}, #{role:string}', ['name' => 'emaphp', 'role' => 'developer'])->build();
		$this->assertEquals("INSERT INTO @@users (name,role) VALUES (#{name:s}, #{role:string})", $sql);
		$this->assertCount(1, $args);
		$this->assertEquals('emaphp', $args[0]['name']);
		$this->assertEquals('developer', $args[0]['role']);
	}
}