<?php
namespace eMapper\MySQL;

use Acme\Type\RGBColorTypeHandler;
use eMapper\SQL\Statement;
use eMapper\Engine\MySQL\MySQLDriver;
use eMapper\Mapper;

/**
 * Generic mysql test class
 * 
 * @author emaphp
 * @group mysql
 */
abstract class MySQLTest extends \PHPUnit_Framework_TestCase {
	/**
	 * MySQL configuration
	 * @var array
	 */
	public static $config = array('host' => 'localhost', 'user' => 'root', 'password' => 'c4lpurn14', 'database' => 'emapper_testing');
	
	/**
	 * Environment configuration
	 * @var array
	 */
	public static $env_config = ['environment.id' => 'default', 'environment.class' => 'eMapper\Dynamic\Environment\DynamicSQLEnvironment'];
	
	/**
	 * MySQL default connection
	 * @var \mysqli
	 */
	public static $conn;
	
	/**
	 * MySQL driver
	 * @var MySQLDriver
	 */
	public static $driver;
	
	/**
	 * MySQLMapper instance
	 * @var MySQLMapper
	 */
	public static $mapper;
	
	public static $blob;
	
	/* (non-PHPdoc)
	 * @see PHPUnit_Framework_TestCase::setUpBeforeClass()
	 */
	public static function setUpBeforeClass() {
		self::$conn = new \mysqli(self::$config['host'], self::$config['user'], self::$config['password'], self::$config['database']);
		self::$driver = new MySQLDriver(self::$config['database'], self::$config['host'], self::$config['user'], self::$config['password']);
		self::$mapper = new Mapper(self::$driver);
		
		//add 'color' type
		self::$mapper->addType('Acme\RGBColor', new RGBColorTypeHandler(), 'color');
		
		//add some statements
		self::$mapper->addStatement(new Statement('getProduct', "SELECT * FROM products WHERE product_id = #{productId}"));		
		self::$mapper->addStatement(new Statement('getUser', "SELECT * FROM users WHERE user_id = %{int}", Statement::type('array')));
		
		//store avatar image
		self::$blob = file_get_contents(__DIR__ . '/../avatar.gif');
	}
	
	public static function tearDownAfterClass() {
		self::$conn->close();
		self::$mapper->close();
	}
}
?>