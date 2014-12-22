<?php
namespace eMapper\Reflection;

use eMapper\Reflection\Argument\ArgumentWrapper;
use Acme\Reflection\Parameter\ExampleUser;
use Acme\Reflection\Parameter\UserEntity;

/**
 * Tests building a parameter wrapper for various types of values
 * 
 * @author emaphp
 * @group reflection
 */
class ParameterMapTest extends \PHPUnit_Framework_TestCase {
	public function testArray() {
		$value = ['name' => 'joe', 'lastname' => 'doe'];
		$wrapper = ArgumentWrapper::wrap($value);
		
		$this->assertInstanceOf('eMapper\Reflection\Argument\ArrayArgumentWrapper', $wrapper);
		$this->assertTrue($wrapper->offsetExists('name'));
		$this->assertTrue($wrapper->offsetExists('lastname'));
		$this->assertEquals('joe', $wrapper['name']);
		$this->assertEquals('doe', $wrapper['lastname']);
	}
	
	public function testArrayObject() {
		$value = new \ArrayObject(['name' => 'joe', 'lastname' => 'doe']);
		$wrapper = ArgumentWrapper::wrap($value);
		
		$this->assertInstanceOf('eMapper\Reflection\Argument\ArrayArgumentWrapper', $wrapper);
		$this->assertTrue($wrapper->offsetExists('name'));
		$this->assertTrue($wrapper->offsetExists('lastname'));
		$this->assertEquals('joe', $wrapper['name']);
		$this->assertEquals('doe', $wrapper['lastname']);
	}
		
	public function testStdClass() {
		$value = new \stdClass();
		$value->name = 'joe';
		$value->lastname = 'doe';
		
		$wrapper = ArgumentWrapper::wrap($value);
		
		$this->assertInstanceOf('eMapper\Reflection\Argument\ArrayArgumentWrapper', $wrapper);
		$this->assertTrue($wrapper->offsetExists('name'));
		$this->assertTrue($wrapper->offsetExists('lastname'));
		$this->assertEquals('joe', $wrapper['name']);
		$this->assertEquals('doe', $wrapper['lastname']);
	}
	
	public function testObject() {
		$value = new ExampleUser('joe', 'doe', '123456');
		$wrapper = ArgumentWrapper::wrap($value);
		
		$this->assertInstanceOf('eMapper\Reflection\Argument\ObjectArgumentWrapper', $wrapper);
		$this->assertTrue($wrapper->offsetExists('name'));
		$this->assertTrue($wrapper->offsetExists('lastname'));
		$this->assertTrue($wrapper->offsetExists('password'));
		$this->assertEquals('joe', $wrapper['name']);
		$this->assertEquals('doe', $wrapper['lastname']);
	}
	
	public function testEntity() {
		$value = new UserEntity();
		$value->name = 'joe';
		$value->surname = 'doe';
		$value->setPassword('123456');
		
		$wrapper = ArgumentWrapper::wrap($value);
		$this->assertInstanceOf('eMapper\Reflection\Argument\ObjectArgumentWrapper', $wrapper);
		$this->assertTrue($wrapper->offsetExists('name'));
		$this->assertTrue($wrapper->offsetExists('surname'));
		$this->assertTrue($wrapper->offsetExists('password'));
		$this->assertFalse($wrapper->offsetExists('lastname'));
		$this->assertEquals('joe', $wrapper['name']);
		$this->assertEquals('doe', $wrapper['surname']);
		$this->assertEquals('123456', $wrapper['password']);
	}
}