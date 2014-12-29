<?php
namespace eMapper\Fluent;

use eMapper\MapperTest;
use eMapper\Query\Column;

abstract class AbstractFluentDeleteTest extends MapperTest {
	public function testWhere() {
		$query = $this->mapper->newQuery();
		list($sql, $_) = $query->deleteFrom('users')->build();
		$this->assertEquals('DELETE FROM @@users', $sql);
		
		$query = $this->mapper->newQuery();
		list($sql, $_) = $query->deleteFrom('users', 'u')->build();
		$this->assertEquals('DELETE FROM @@users u', $sql);
		
		$query = $this->mapper->newQuery();
		list($sql, $_) = $query->deleteFrom('users')->where('id = 1')->build();
		$this->assertEquals('DELETE FROM @@users WHERE id = 1', $sql);
		
		$query = $this->mapper->newQuery();
		list($sql, $args) = $query->deleteFrom('users')->where('id = %{i}', 1)->build();
		$this->assertEquals('DELETE FROM @@users WHERE id = %{i}', $sql);
		$this->assertCount(1, $args);
		$this->assertEquals(1, $args[0]);
		
		$query = $this->mapper->newQuery();
		list($sql, $args) = $query->deleteFrom('users')->where(Column::id()->eq(1))->build();
		$this->assertRegExp('/DELETE FROM @@users WHERE id = #\{\$\d+}/', $sql);
		$this->assertCount(1, $args);
		$this->assertInternalType('array', $args[0]);
		$key = key($args[0]);
		$this->assertEquals(1, $args[0][$key]);
		
		$query = $this->mapper->newQuery();
		list($sql, $args) = $query->deleteFrom('users')->where('id = %{i} OR name LIKE %{s}', 1, '%ema%')->build();
		$this->assertEquals('DELETE FROM @@users WHERE id = %{i} OR name LIKE %{s}', $sql);
		$this->assertCount(2, $args);
		$this->assertEquals(1, $args[0]);
		$this->assertEquals('%ema%', $args[1]);
		
		$query = $this->mapper->newQuery();
		list($sql, $args) = $query->deleteFrom('users')->where(Column::id()->eq(1), Column::name()->isnull())->build();
		$this->assertRegExp('/DELETE FROM @@users WHERE \( id = #\{\$\d+} AND name IS NULL \)/', $sql);
		$this->assertCount(1, $args);
		$this->assertInternalType('array', $args[0]);
		$key = key($args[0]);
		$this->assertEquals(1, $args[0][$key]);
	}
	
	public function testJoin() {
		$query = $this->mapper->newQuery();
		list($sql, $_) = $query->deleteFrom('users')
		->innerJoin('profiles', 'profiles.user_id = users.id')
		->build();
		
		$this->assertEquals('DELETE FROM @@users INNER JOIN @@profiles ON profiles.user_id = users.id', $sql);
		
		$query = $this->mapper->newQuery();
		list($sql, $_) = $query->deleteFrom('users', 'u')
		->innerJoin('profiles', 'p', 'p.user_id = u.id')
		->build();
		
		$this->assertEquals('DELETE FROM @@users u INNER JOIN @@profiles p ON p.user_id = u.id', $sql);
		
		$query = $this->mapper->newQuery();
		list($sql, $_) = $query->deleteFrom('users', 'u')
		->innerJoin('profiles', Column::u__id()->eq(Column::profiles__user_id()))
		->build();
		
		$this->assertEquals('DELETE FROM @@users u INNER JOIN @@profiles ON u.id = profiles.user_id', $sql);
	}
}
?>