<?php
namespace eMapper\Fluent;

use eMapper\MapperTest;
use eMapper\Query\Column;
use eMapper\Query\Func as F;

abstract class AbstractFluentSelectTest extends MapperTest {
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
		
		$query = $this->mapper->newQuery();
		list($sql, $_) = $query->from('users', 'u')->select(Column::u__id()->alias('user_id'), Column::u__email())->build();
		$this->assertEquals("SELECT u.id AS user_id,u.email FROM users u", $sql);
	}
	
	/**
	 * @expectedException UnexpectedValueException
	 */
	public function testColumnsError() {
		$query = $this->mapper->newQuery();
		list($sql, $_) = $query->from('users', 'u')->select(Column::test__error())->build();
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
	
	/**
	 * @expectedException UnexpectedValueException
	 */
	public function testOrderByError() {
		$query = $this->mapper->newQuery();
		list($sql, $_) = $query->from('users', 'u')->orderBy(Column::test__error('DESC'))->build();
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
		->leftJoin('profiles', 'u.pid = profiles.id')
		->build();
		$this->assertEquals("SELECT * FROM users u LEFT JOIN profiles ON u.pid = profiles.id", $sql);
	
		$query = $this->mapper->newQuery();
		list($sql, $_) = $query->from('users')
		->fullOuterJoin('profiles', 'p', 'users.pid = p.id')
		->build();
		$this->assertEquals("SELECT * FROM users FULL OUTER JOIN profiles p ON users.pid = p.id", $sql);
	
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
		->select(Column::u__name()->alias('username'), Column::profiles__email(), Column::id())
		->build();
		$this->assertEquals("SELECT u.name AS username,profiles.email,u.id FROM users u INNER JOIN profiles ON u.pid = profiles.id", $sql);
	
		$query = $this->mapper->newQuery();
		list($sql, $_) = $query->from('users', 'u')
		->innerJoin('profiles', 'p', 'u.pid = p.id')
		->select(Column::u__name(), Column::p__email(), Column::id())
		->build();
		$this->assertEquals("SELECT u.name,p.email,u.id FROM users u INNER JOIN profiles p ON u.pid = p.id", $sql);
	
		//join condition
		$query = $this->mapper->newQuery();
		list($sql, $args) = $query->from('users')
		->innerJoin('profiles', Column::users__name()->eq('test'))
		->build();
		$this->assertRegExp("/^SELECT \* FROM users INNER JOIN profiles ON users\.name = #\{arg\d+\}$/", $sql);
		$this->assertInternalType('array', $args);
		$this->assertCount(1, $args);
		$this->assertInternalType('array', $args[0]);
		$key = key($args[0]);
		$this->assertEquals('test', $args[0][$key]);
	
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
	
	public function testWhere() {
		$query = $this->mapper->newQuery();
		list($sql, $_) = $query->from('users')->where('id = 1')->build();
		$this->assertEquals("SELECT * FROM users WHERE id = 1", $sql);
		
		$query = $this->mapper->newQuery();
		list($sql, $args) = $query->from('users')->where('id = %{i}', 1)->build();
		$this->assertEquals("SELECT * FROM users WHERE id = %{i}", $sql);
		$this->assertInternalType('array', $args);
		$this->assertArrayHasKey(0, $args);
		$this->assertCount(1, $args);
		$this->assertEquals(1, $args[0]);
		
		$query = $this->mapper->newQuery();
		list($sql, $args) = $query->from('users')->where(Column::id()->eq(1))->build();
		$this->assertRegExp("/^SELECT \* FROM users WHERE id = #\{arg\d+\}$/", $sql);
		$this->assertInternalType('array', $args);
		$this->assertCount(1, $args);
		$key = key($args[0]);
		$this->assertEquals(1, $args[0][$key]);
	}
	
	public function testHaving() {
		$query = $this->mapper->newQuery();
		list($sql, $args) = $query->from('Employees', 'emp')
		->innerJoin('Orders', 'ord', 'ord.employee_id = emp.id')
		->select('emp.lastname', 'COUNT(ord.id)')
		->groupBy('emp.lastname')
		->having('COUNT(ord.id) > %{i}', 10)
		->build();
		
		$this->assertEquals('SELECT emp.lastname,COUNT(ord.id) FROM Employees emp INNER JOIN Orders ord ON ord.employee_id = emp.id GROUP BY emp.lastname HAVING COUNT(ord.id) > %{i}', $sql);
		$this->assertCount(1, $args);
		$this->assertEquals(10, $args[0]);
		
		$query = $this->mapper->newQuery();
		list($sql, $args) = $query->from('Employees', 'emp')
		->innerJoin('Orders', 'ord', Column::ord__employee_id()->eq(Column::emp__id()))
		->select(Column::emp__lastname(), F::COUNT(Column::ord__id()))
		->groupBy(Column::emp__lastname())
		->having(F::COUNT(Column::ord__id())->gt(10))
		->build();
		
		$this->assertRegExp('/SELECT emp\.lastname,COUNT\(ord\.id\) FROM Employees emp INNER JOIN Orders ord ON ord\.employee_id = emp\.id GROUP BY emp\.lastname HAVING COUNT\(ord\.id\) > #\{arg\d+\}/', $sql);
		$this->assertCount(1, $args);
		$this->assertInternalType('array', $args[0]);
		$key = key($args[0]);
		$this->assertEquals(10, $args[0][$key]);
	}
	
	public function testFunction() {
		//COUNT
		$query = $this->mapper->newQuery();
		list($sql, $_) = $query->from('users')
		->select(F::COUNT('*'))
		->build();
		$this->assertEquals('SELECT COUNT(*) FROM users', $sql);
		
		//UCASE
		$query = $this->mapper->newQuery();
		list($sql, $_) = $query->from('Customers')
		->select(F::UCASE(Column::CustomerName())->alias('Customer'), Column::City())
		->build();
		$this->assertEquals('SELECT UCASE(CustomerName) AS Customer,City FROM Customers', $sql);
		
		//MID
		$query = $this->mapper->newQuery();
		list($sql, $_) = $query->from('Customers')
		->select(F::MID(Column::City(), 1, 4)->alias('ShortCity'))
		->build();
		$this->assertEquals('SELECT MID(City,1,4) AS ShortCity FROM Customers', $sql);
		
		//LEN
		$query = $this->mapper->newQuery();
		list($sql, $args) = $query->from('users')
		->where(F::LEN(Column::email())->lt(20))
		->build();
		
		$this->assertRegExp('/SELECT \* FROM users WHERE LEN\(email\) < #\{arg\d+\}/', $sql);
		$this->assertCount(1, $args);
		$this->assertInternalType('array', $args[0]);
		$key = key($args[0]);
		$this->assertEquals(20, $args[0][$key]);
		
		//ROUND
		$query = $this->mapper->newQuery();
		list($sql, $_) = $query->from('Products')
		->select('ProductName', F::ROUND(Column::Price(), 2)->alias('RoundedPrice'))
		->build();
		
		$this->assertEquals('SELECT ProductName,ROUND(Price,2) AS RoundedPrice FROM Products', $sql);
		
		//NOW
		$query = $this->mapper->newQuery();
		list($sql, $_) = $query->from('Products')
		->select('ProductName', Column::Price(), F::NOW()->alias('PerDate'))
		->build();
		
		$this->assertEquals('SELECT ProductName,Price,NOW() AS PerDate FROM Products', $sql);
		
		//FORMAT
		$query = $this->mapper->newQuery();
		list($sql, $_) = $query->from('Products')
		->select('ProductName', Column::Price(), F::FORMAT(F::NOW(), '"YYYY-MM-DD"')->alias('PerDate'))
		->build();
		
		$this->assertEquals('SELECT ProductName,Price,FORMAT(NOW(),"YYYY-MM-DD") AS PerDate FROM Products', $sql);
	}
}
?>