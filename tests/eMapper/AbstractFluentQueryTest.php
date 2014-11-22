<?php
namespace eMapper;

use eMapper\Query\Column;

abstract class AbstractFluentQueryTest extends MapperTest {
	public function testColumns() {
		$query = $this->mapper->newQuery();
		list($sql, $_) = $query->from('users')->select('id', 'email')->build();
		$this->assertEquals("SELECT id,email FROM users", $sql);
		
		$query = $this->mapper->newQuery();
		list($sql, $_) = $query->from('users', 'u')->select('u.id', 'u.email')->build();
		$this->assertEquals("SELECT u.id,u.email FROM users u", $sql);
		
		$query = $this->mapper->newQuery();
		list($sql, $_) = $query->from('users', 'u')->select('id', 'email')->build();
		$this->assertEquals("SELECT id,email FROM users u", $sql);
		
		$query = $this->mapper->newQuery();
		list($sql, $_) = $query->from('users')->select(Column::id(), Column::email())->build();
		$this->assertEquals("SELECT id,email FROM users", $sql);
		
		list($sql, $_) = $query->from('users', 'u')->select(Column::id(), Column::email())->build();
		$this->assertEquals("SELECT u.id,u.email FROM users u", $sql);
		
		$query = $this->mapper->newQuery();
		list($sql, $_) = $query->from('users')->select(Column::users__id(), Column::users__email())->build();
		$this->assertEquals("SELECT users.id,users.email FROM users", $sql);
		
		$query = $this->mapper->newQuery();
		list($sql, $_) = $query->from('users', 'u')->select(Column::u__id(), Column::u__email())->build();
		$this->assertEquals("SELECT u.id,u.email FROM users u", $sql);
	}
	
	public function testOrderBy() {
		$query = $this->mapper->newQuery();
		list($sql, $_) = $query->from('users')->orderBy('id')->build();
		$this->assertEquals("SELECT * FROM users ORDER BY id", $sql);
		
		$query = $this->mapper->newQuery();
		list($sql, $_) = $query->from('users')->orderBy('users.id')->build();
		$this->assertEquals("SELECT * FROM users ORDER BY users.id", $sql);
		
		$query = $this->mapper->newQuery();
		list($sql, $_) = $query->from('users', 'u')->orderBy('id')->build();
		$this->assertEquals("SELECT * FROM users u ORDER BY id", $sql);
		
		$query = $this->mapper->newQuery();
		list($sql, $_) = $query->from('users')->orderBy(Column::id())->build();
		$this->assertEquals("SELECT * FROM users ORDER BY id", $sql);
		
		$query = $this->mapper->newQuery();
		list($sql, $_) = $query->from('users', 'u')->orderBy(Column::id())->build();
		$this->assertEquals("SELECT * FROM users u ORDER BY u.id", $sql);
		
		$query = $this->mapper->newQuery();
		list($sql, $_) = $query->from('users')->orderBy(Column::users__id())->build();
		$this->assertEquals("SELECT * FROM users ORDER BY users.id", $sql);
		
		$query = $this->mapper->newQuery();
		list($sql, $_) = $query->from('users', 'u')->orderBy(Column::u__id())->build();
		$this->assertEquals("SELECT * FROM users u ORDER BY u.id", $sql);
		
		//order by + type
		$query = $this->mapper->newQuery();
		list($sql, $_) = $query->from('users')->orderBy('id ASC')->build();
		$this->assertEquals("SELECT * FROM users ORDER BY id ASC", $sql);
		
		$query = $this->mapper->newQuery();
		list($sql, $_) = $query->from('users')->orderBy('users.id DESC')->build();
		$this->assertEquals("SELECT * FROM users ORDER BY users.id DESC", $sql);
		
		$query = $this->mapper->newQuery();
		list($sql, $_) = $query->from('users', 'u')->orderBy('id ASC')->build();
		$this->assertEquals("SELECT * FROM users u ORDER BY id ASC", $sql);
		
		$query = $this->mapper->newQuery();
		list($sql, $_) = $query->from('users')->orderBy(Column::id('ASC'))->build();
		$this->assertEquals("SELECT * FROM users ORDER BY id ASC", $sql);
		
		$query = $this->mapper->newQuery();
		list($sql, $_) = $query->from('users', 'u')->orderBy(Column::id('DESC'))->build();
		$this->assertEquals("SELECT * FROM users u ORDER BY u.id DESC", $sql);
		
		$query = $this->mapper->newQuery();
		list($sql, $_) = $query->from('users')->orderBy(Column::users__id('ASC'))->build();
		$this->assertEquals("SELECT * FROM users ORDER BY users.id ASC", $sql);
		
		$query = $this->mapper->newQuery();
		list($sql, $_) = $query->from('users', 'u')->orderBy(Column::u__id('DESC'))->build();
		$this->assertEquals("SELECT * FROM users u ORDER BY u.id DESC", $sql);
	}
	
	public function testLimitOffset() {
		$query = $this->mapper->newQuery();
		list($sql, $_) = $query->from('users')->limit(10)->build();
		$this->assertEquals("SELECT * FROM users LIMIT 10", $sql);
		
		$query = $this->mapper->newQuery();
		list($sql, $_) = $query->from('users')->offset(10)->build();
		$this->assertEquals("SELECT * FROM users OFFSET 10", $sql);
		
		$query = $this->mapper->newQuery();
		list($sql, $_) = $query->from('users')->limit(10)->offset(5)->build();
		$this->assertEquals("SELECT * FROM users LIMIT 10 OFFSET 5", $sql);
	}
	
	public function testJoins() {
		$query = $this->mapper->newQuery();
		list($sql, $_) = $query->from('users')
		->innerJoin('profiles', 'users.pid = profiles.id')
		->build();
		$this->assertEquals("SELECT * FROM users INNER JOIN profiles ON users.pid = profiles.id", $sql);
		
		$query = $this->mapper->newQuery();
		list($sql, $_) = $query->from('users', 'u')
		->innerJoin('profiles', 'u.pid = profiles.id')
		->build();
		$this->assertEquals("SELECT * FROM users u INNER JOIN profiles ON u.pid = profiles.id", $sql);
		
		$query = $this->mapper->newQuery();
		list($sql, $_) = $query->from('users')
		->innerJoin('profiles', 'p', 'users.pid = p.id')
		->build();
		$this->assertEquals("SELECT * FROM users INNER JOIN profiles p ON users.pid = p.id", $sql);
		
		//joins + Column
		$query = $this->mapper->newQuery();
		list($sql, $_) = $query->from('users')
		->innerJoin('profiles', 'users.pid = profiles.id')
		->select(Column::users__name(), Column::profiles__email(), Column::id())
		->build();
		$this->assertEquals("SELECT users.name,profiles.email,id FROM users INNER JOIN profiles ON users.pid = profiles.id", $sql);
		
		$query = $this->mapper->newQuery();
		list($sql, $_) = $query->from('users', 'u')
		->innerJoin('profiles', 'u.pid = profiles.id')
		->select(Column::u__name(), Column::profiles__email(), Column::id())
		->build();
		$this->assertEquals("SELECT u.name,profiles.email,u.id FROM users u INNER JOIN profiles ON u.pid = profiles.id", $sql);
		
		$query = $this->mapper->newQuery();
		list($sql, $_) = $query->from('users', 'u')
		->innerJoin('profiles', 'p', 'u.pid = p.id')
		->select(Column::u__name(), Column::p__email(), Column::id())
		->build();
		$this->assertEquals("SELECT u.name,p.email,u.id FROM users u INNER JOIN profiles p ON u.pid = p.id", $sql);
		
		//join condition
		$query = $this->mapper->newQuery();
		list($sql, $_) = $query->from('users')
		->innerJoin('profiles', Column::users__name()->eq('test'))
		->build();
		$this->assertRegExp("/^SELECT \* FROM users INNER JOIN profiles ON users\.name = #\{arg\d+\}$/", $sql);
		
		$query = $this->mapper->newQuery();
		list($sql, $_) = $query->from('users')
		->innerJoin('profiles', Column::users__name()->eq(Column::users__lastname()))
		->build();
		$this->assertEquals("SELECT * FROM users INNER JOIN profiles ON users.name = users.lastname", $sql);
		
		$query = $this->mapper->newQuery();
		list($sql, $_) = $query->from('users', 'u')
		->innerJoin('profiles', 'p', Column::p__name()->eq(Column::u__lastname()))
		->build();
		$this->assertEquals("SELECT * FROM users u INNER JOIN profiles p ON p.name = u.lastname", $sql);
	}
}
?>