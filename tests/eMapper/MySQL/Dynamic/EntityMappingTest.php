<?php
namespace eMapper\MySQL\Dynamic;

use eMapper\MySQL\MySQLTest;
use Acme\Result\Dynamic\User;

/**
 * 
 * @author emaphp
 * @group attribute
 */
class EntityMappingTest extends MySQLTest {
	public function testMacroAttribute() {
		$user = self::$mapper->type('obj:Acme\Result\Dynamic\User')->query("SELECT * FROM users WHERE user_id = 1");
		$this->assertInstanceOf('Acme\Result\Dynamic\User', $user);
		
		//id
		$this->assertInternalType('integer', $user->id);
		$this->assertEquals(1, $user->id);
		
		//name
		$this->assertInternalType('string', $user->user_name);
		$this->assertEquals('jdoe', $user->user_name);
		
		//birthDate
		$this->assertInstanceOf('DateTime', $user->getBirthDate());
		$this->assertEquals('1987-08-10', $user->getBirthDate()->format('Y-m-d'));
		
		/**
		 * DYNAMIC ATTRIBUTES
		 */
		//uppercase_name
		$this->assertInternalType('string', $user->uppercase_name);
		$this->assertEquals('JDOE', $user->uppercase_name);
		
		//fakeId
		$this->assertInternalType('integer', $user->fakeId);
		$this->assertEquals(6, $user->fakeId);
		
		//age
		$this->assertInternalType('string', $user->age);
		$this->assertEquals('26', $user->age);
	}
}
?>