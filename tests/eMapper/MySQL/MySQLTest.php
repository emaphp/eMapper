<?php
namespace eMapper\MySQL;

use eMapper\Engine\MySQL\MySQLMapper;
/**
 * 
 * @author emaphp
 * @group mysql
 */
abstract class MySQLTest extends \PHPUnit_Framework_TestCase {
	/**
	 * MySQL configuration
	 * @var array
	 */
	public static $config = array('host' => 'localhost', 'user' => 'root', 'password' => 'b0ls0d10s', 'database' => 'emapper_testing');
	
	/**
	 * MySQL default connection
	 * @var \mysqli
	 */
	public static $conn;
	
	public static $mapper;
	
	/* (non-PHPdoc)
	 * @see PHPUnit_Framework_TestCase::setUpBeforeClass()
	 */
	public static function setUpBeforeClass() {
		self::$conn = new \mysqli(self::$config['host'], self::$config['user'], self::$config['password'], self::$config['database']);
		
		self::$mapper = new MySQLMapper(self::$config['database'],
				self::$config['host'],
				self::$config['user'],
				self::$config['password']);
	}
	
	public static function tearDownAfterClass() {
		self::$conn->close();
	}
}
?>