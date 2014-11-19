<?php
namespace eMapper\PostgreSQL;

use Acme\Type\RGBColorTypeHandler;
use eMapper\SQL\Statement;
use eMapper\Engine\PostgreSQL\PostgreSQLDriver;
use eMapper\Mapper;

abstract class PostgreSQLTest extends \PHPUnit_Framework_TestCase {
	public static $env_config = ['environment.id' => 'default', 'environment.class' => 'eMapper\Dynamic\Environment\DynamicSQLEnvironment'];
	public static $connstring = 'host=localhost port=5432 dbname=emapper_testing user=postgres password=c4lpurn14';
	public static $conn;
	public static $driver;
	public static $mapper;
	public static $blob;
	
	public static function setUpBeforeClass() {
		self::$conn = pg_connect(self::$connstring);
		self::$driver = new PostgreSQLDriver(self::$connstring);
		self::$mapper = new Mapper(self::$driver);
		self::$mapper->addType('Acme\RGBColor', new RGBColorTypeHandler(), 'color');
		
		self::$blob = file_get_contents(__DIR__ . '/../avatar.gif');
	}
	
	public static function tearDownAfterClass() {
		//TODO: this breaks some tests, try running --debug
		//pg_close(self::$conn);
		self::$mapper->close();
	}
}

?>