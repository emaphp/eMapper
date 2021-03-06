<?php
namespace eMapper\PostgreSQL;

/**
 * Tests binary insertion in a PostgreSQL database
 * @author emaphp
 * @group postgre
 * @group blob
 */
class BlobInsertTest extends PostgreSQLTest {
	public function testArray() {
		$result = self::$mapper->type('array')->query("SELECT * FROM users_findall()");
	}
	
	public function __testInsertion() {
		//users table
		self::$mapper->query("INSERT INTO users VALUES (DEFAULT, %{s}, %{s}, %{s}, %{s}, %{x})",
							'jdoe', '1987-08-10', '2013-08-10 19:57:15', '12:00:00', self::$blob);
		
		self::$mapper->query("INSERT INTO users VALUES (DEFAULT, %{s}, %{s}, %{s}, %{s}, %{x})",
							'okenobi', '1976-03-03', '2013-01-06 12:34:10', '00:00:00', self::$blob);
		
		self::$mapper->query("INSERT INTO users VALUES (DEFAULT, %{s}, %{s}, %{s}, %{s}, %{x})",
							'jkirk', '1967-11-21', '2013-02-16 20:00:33', '17:00:00', self::$blob);
		
		self::$mapper->query("INSERT INTO users VALUES (DEFAULT, %{s}, %{s}, %{s}, %{s}, %{x})",
							'egoldstein', '1980-12-07', '2013-03-26 10:01:45', '13:30:00', self::$blob);
		
		self::$mapper->query("INSERT INTO users VALUES (DEFAULT, %{s}, %{s}, %{s}, %{s}, %{x})",
							'ishmael', '1977-03-16', '2013-05-22 14:23:32', '11:30:00', self::$blob);
		
		//products table
		self::$mapper->query("INSERT INTO products VALUES (DEFAULT, %{s}, %{s}, %{s}, %{f}, %{s}, %{f}, %{b}, %{i})",
							'IND00054', 'Red dress', 'e11a1a', 150.65, 'Clothes', 4.5, false, 2011);
		
		self::$mapper->query("INSERT INTO products VALUES (DEFAULT, %{s}, %{s}, %{s}, %{f}, %{s}, %{f}, %{b}, %{i})",
							'IND00043', 'Blue jeans', '0c1bd9', 235.7, 'Clothes', 3.9, false, 2012);
		
		self::$mapper->query("INSERT INTO products VALUES (DEFAULT, %{s}, %{s}, %{s}, %{f}, %{s}, %{f}, %{b}, %{i})",
							'IND00232', 'Green shirt', '707c04', 70.9, 'Clothes', 4.1, false, 2013);
		
		self::$mapper->query("INSERT INTO products VALUES (DEFAULT, %{s}, %{s}, %{s}, %{f}, %{s}, %{f}, %{b}, %{i})",
							'GFX00067', 'ATI HD 9999', null, 120.75, 'Hardware', 3.8, false, 2013);
		
		self::$mapper->query("INSERT INTO products VALUES (DEFAULT, %{s}, %{s}, %{s}, %{f}, %{s}, %{f}, %{b}, %{i})",
							'PHN00098', 'Android phone', '00a7eb', 300.3, 'Smartphones', 4.8, true, 2011);
		
		//sales table
		self::$mapper->query("INSERT INTO sales VALUES (DEFAULT, %{i}, %{i}, %{s}, %{f})", 5, 1, '2013-08-10 20:37:18', 0.25);
		self::$mapper->query("INSERT INTO sales VALUES (DEFAULT, %{i}, %{i}, %{s}, %{f})", 2, 5, '2013-05-17 14:22:50', 0.15);
		self::$mapper->query("INSERT INTO sales VALUES (DEFAULT, %{i}, %{i}, %{s}, %{f})", 4, 2, '2013-02-28 12:39:53', 0.12);
		self::$mapper->query("INSERT INTO sales VALUES (DEFAULT, %{i}, %{i}, %{s}, %{f})", 3, 3, '2013-07-05 17:34:12', 0.1);
	}
}
?>