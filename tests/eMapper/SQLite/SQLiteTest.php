<?php
namespace eMapper\SQLite;

use Acme\Type\RGBColorTypeHandler;
use eMapper\SQL\Statement;
use eMapper\Engine\SQLite\SQLiteDriver;
use eMapper\Mapper;

abstract class SQLiteTest extends \PHPUnit_Framework_TestCase {
	public static $env_config = ['environment.id' => 'default', 'environment.class' => 'eMapper\Dynamic\Environment\DynamicSQLEnvironment'];
	
	public static $filename;
	public static $conn;
	public static $driver;
	public static $mapper;
	public static $blob;
	
	public static function setUpBeforeClass() {
		self::$filename = __DIR__ . '/testing.db';
		self::$conn = new \SQLite3(self::$filename);
		self::$driver = new SQLiteDriver(self::$filename);
		self::$mapper = new Mapper(self::$driver);
		self::$mapper->addType('Acme\RGBColor', new RGBColorTypeHandler(), 'color');
		self::$mapper->addStatement(new Statement('getProduct', "SELECT * FROM products WHERE product_id = #{productId}"));
		self::$mapper->addStatement(new Statement('getUser', "SELECT * FROM users WHERE user_id = %{int}", Statement::type('array')));
		
		self::$blob = file_get_contents(__DIR__ . '/../avatar.gif');
	}
	
	public static function tearDownAfterClass() {
		self::$conn->close();
		self::$mapper->close();
	}
}
?>