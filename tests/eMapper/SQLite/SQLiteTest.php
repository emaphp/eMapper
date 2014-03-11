<?php
namespace eMapper\SQLite;

use eMapper\Engine\SQLite\SQLiteMapper;

abstract class SQLiteTest extends \PHPUnit_Framework_TestCase {
	public static $filename;
	public static $conn;
	public static $mapper;
	
	public static function setUpBeforeClass() {
		self::$filename = __DIR__ . '/testing.db';
		self::$conn = new \SQLite3(self::$filename);
		self::$mapper = new SQLiteMapper(self::$filename);
	}
	
	public static function tearDownAfterClass() {
		self::$conn->close();
		self::$mapper->close();
	}
}
?>