<?php
namespace eMapper\MySQL;

use eMapper\Engine\MySQL\Statement\MySQLStatement;
use eMapper\Type\TypeManager;

/**
 * 
 * @author emaphp
 * @group mysql
 */
class DynamicSQLTest extends MySQLTest {
	public $statement;
	
	public function __construct() {
		self::setUpBeforeClass();
		$this->statement = new MySQLStatement(self::$conn, new TypeManager(), null);
	}
	
	public function testSimpleArgument() {
		$result = $this->statement->build('[[ (if (int? (%0)) "%{i}" "%{s}") ]]', array(25), self::$env_config);
		$this->assertEquals(25, $result);
		
		$result = $this->statement->build('[[ (if (int? (%0)) "%{i}" "%{s}") ]]', array('joe'), self::$env_config);
		$this->assertEquals("'joe'", $result);
		
		$result = $this->statement->build('SELECT * FROM [[ (if (int? (%0)) "user_id = %{i}") ]]', array('joe'), self::$env_config);
		$this->assertEquals('SELECT * FROM ', $result);
		
		$result = $this->statement->build('SELECT * FROM users WHERE [[ (if (int? (%0)) "user_id = %{i}" "user_name = %{s}") ]]', array(25), self::$env_config);
		$this->assertEquals("SELECT * FROM users WHERE user_id = 25", $result);
		
		$result = $this->statement->build('SELECT * FROM users WHERE [[ (if (string? (%0)) "user_name = %{s}" "user_id = %{i}") ]]', array('joe'), self::$env_config);
		$this->assertEquals("SELECT * FROM users WHERE user_name = 'joe'", $result);
		
		$result = $this->statement->build('ORDER BY [[ (. (%0) " " (strtoupper (%1))) ]]', array('name', 'desc'), self::$env_config);
		$this->assertEquals("ORDER BY name DESC", $result);
	}
	
	public function testComplexArgument() {
		$result = $this->statement->build('ORDER BY [[ (. (if (#order_field?) (#order_field) "user_name") " " (if (#order_type?) (strtoupper (#order_type)) "DESC")) ]]', array(['order_field' => 'user_id']), self::$env_config);
		$this->assertEquals("ORDER BY user_id DESC", $result);
		
		$result = $this->statement->build('ORDER BY [[ (. (if (#order_field?) (#order_field) "user_name") " " (if (#order_type?) (strtoupper (#order_type)) "DESC"))]]', array(['order_field' => 'user_id', 'order_type' => 'asc']), self::$env_config);
		$this->assertEquals("ORDER BY user_id ASC", $result);
	}
}
?>