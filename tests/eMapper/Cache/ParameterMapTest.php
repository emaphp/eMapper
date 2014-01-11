<?php
namespace eMapper\Cache;

use eMapper\Type\TypeManager;
use eMapper\Cache\Key\CacheKey;
use Acme\Entity\User;

/**
 * 
 * @author emaphp
 * @group cache
 */
class ParameterMapTest extends \PHPUnit_Framework_TestCase {
	/**
	 * Tests a parameter map applied to an array
	 */
	public function testArray() {
		$cacheKey = new CacheKey(new TypeManager(), 'Acme\Parameter\ProductParameterMap');
		$args = array(array('pcod' => 'ABC123', 'price' => 46.25, 'refurbished' => 1));
		$result = $cacheKey->build("cod: #{code[1..3]}, price: #{cost:i}, ref: #{refurbished}", $args, null);
		$this->assertEquals("cod: BC1, price: 46, ref: TRUE", $result);
	}
	
	/**
	 * Tests a parameter map applied to an instance of ArrayObject
	 */
	public function testArrayObject() {
		$cacheKey = new CacheKey(new TypeManager(), 'Acme\Parameter\ProductParameterMap');
		$arr = new \ArrayObject(array('pcod' => 'ABC123', 'price' => 46.25, 'refurbished' => 't'));
		$result = $cacheKey->build("cod: #{code[..3]}, price: #{cost}, ref: #{refurbished}", array($arr), null);
		$this->assertEquals("cod: ABC, price: 46.25, ref: TRUE", $result);
	}
	
	/**
	 * Tests a parameter map applied to an instance of stdClass
	 */
	public function testStdClass() {
		$cacheKey = new CacheKey(new TypeManager(), 'Acme\Parameter\ProductParameterMap');
		
		$instance = new \stdClass();
		$instance->pcod = 'ABC123';
		$instance->price = 34.53;
		$instance->refurbished = '';
		
		$args = array($instance);
		$result = $cacheKey->build("cod: #{code[3]}, price: #{cost}, ref: #{refurbished}", $args, null);
		$this->assertEquals("cod: 1, price: 34.53, ref: FALSE", $result);
	}
	
	/**
	 * Tests parameters obtained from an entity object
	 */
	public function testEntity() {
		$cacheKey = new CacheKey(new TypeManager());
		
		$user = new User();
		$user->id = 4123;
		$user->birthDate = \DateTime::createFromFormat('Y-m-d', "1986-12-22");
		$user->setName('emma');
		
		$result = $cacheKey->build("ID: #{id:s}, #{birthDate:date}, #{name}", array($user), null);
		$this->assertEquals("ID: 4123, 1986-12-22, emma", $result);
	}
}
?>