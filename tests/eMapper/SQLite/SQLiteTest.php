<?php
namespace eMapper\SQLite;

use eMapper\Engine\SQLite\SQLiteMapper;
use Acme\Type\RGBColorTypeHandler;

abstract class SQLiteTest extends \PHPUnit_Framework_TestCase {
	public static $env_config = ['environment.id' => 'default', 'environment.class' => 'eMapper\Dynamic\Environment\DynamicSQLEnvironment'];
	
	public static $filename;
	public static $conn;
	public static $mapper;
	public static $blob;
	
	public static function setUpBeforeClass() {
		self::$filename = __DIR__ . '/testing.db';
		self::$conn = new \SQLite3(self::$filename);
		
		self::$mapper = new SQLiteMapper(self::$filename);
		self::$mapper->addType('Acme\RGBColor', new RGBColorTypeHandler(), 'color');
		
		self::$blob = file_get_contents(__DIR__ . '/../avatar.gif');
	}
	
	public static function tearDownAfterClass() {
		self::$conn->close();
		self::$mapper->close();
	}
}
?>