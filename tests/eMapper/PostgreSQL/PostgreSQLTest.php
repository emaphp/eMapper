<?php
namespace eMapper\PostgreSQL;

use eMapper\Engine\PostgreSQL\PostgreSQLMapper;
use Acme\Type\RGBColorTypeHandler;
use eMapper\SQL\Statement;

abstract class PostgreSQLTest extends \PHPUnit_Framework_TestCase {
	public static $env_config = ['environment.id' => 'default', 'environment.class' => 'eMapper\Dynamic\Environment\DynamicSQLEnvironment'];
	public static $connstring = 'host=localhost port=5432 dbname=emapper_testing user=postgres password=b0ls0d10s';
	public static $conn;
	public static $mapper;
	public static $blob;
	
	public static function setUpBeforeClass() {
		self::$conn = pg_connect(self::$connstring);
		
		self::$mapper = new PostgreSQLMapper(self::$connstring);
		self::$mapper->addType('Acme\RGBColor', new RGBColorTypeHandler(), 'color');
		self::$mapper->addStatement(new Statement('getProduct', "SELECT * FROM products WHERE product_id = #{productId}"));
		self::$mapper->addStatement(new Statement('getUser', "SELECT * FROM users WHERE user_id = %{int}", Statement::type('array')));
		
		self::$blob = file_get_contents(__DIR__ . '/../avatar.gif');
	}
	
	public static function tearDownAfterClass() {
		pg_close(self::$conn);
		self::$mapper->close();
	}
}

?>