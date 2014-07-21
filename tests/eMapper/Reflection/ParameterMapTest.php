<?php
namespace eMapper\Reflection;

use eMapper\Reflection\Parameter\ParameterWrapper;
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
		$wrapper = ParameterWrapper::wrapValue($value);
		
		$this->assertInstanceOf('eMapper\Reflection\Parameter\ArrayParameterWrapper', $wrapper);
		$this->assertTrue($wrapper->offsetExists('name'));
		$this->assertTrue($wrapper->offsetExists('lastname'));
		$this->assertEquals('joe', $wrapper['name']);
		$this->assertEquals('doe', $wrapper['lastname']);
		
		$vars = $wrapper->getValueAsArray();
		$this->assertInternalType('array', $vars);
		$this->assertArrayHasKey('name', $vars);
		$this->assertArrayHasKey('lastname', $vars);
		$this->assertEquals('joe', $vars['name']);
		$this->assertEquals('doe', $vars['lastname']);
	}
	
	public function testArrayParameterMap() {
		$value = ['name' => 'joe', 'lastname' => 'doe'];
		$wrapper = ParameterWrapper::wrapValue($value, 'Acme\Reflection\Parameter\UserArrayParameterMap');
		
		$this->assertInstanceOf('eMapper\Reflection\Parameter\ArrayParameterWrapper', $wrapper);
		$this->assertTrue($wrapper->offsetExists('name'));
		$this->assertTrue($wrapper->offsetExists('surname'));
		$this->assertFalse($wrapper->offsetExists('lastname'));
		$this->assertEquals('joe', $wrapper['name']);
		$this->assertEquals('doe', $wrapper['surname']);
	}
	
	public function testArrayObject() {
		$value = new \ArrayObject(['name' => 'joe', 'lastname' => 'doe']);
		$wrapper = ParameterWrapper::wrapValue($value);
		
		$this->assertInstanceOf('eMapper\Reflection\Parameter\ArrayParameterWrapper', $wrapper);
		$this->assertTrue($wrapper->offsetExists('name'));
		$this->assertTrue($wrapper->offsetExists('lastname'));
		$this->assertEquals('joe', $wrapper['name']);
		$this->assertEquals('doe', $wrapper['lastname']);
		
		$vars = $wrapper->getValueAsArray();
		$this->assertInternalType('array', $vars);
		$this->assertArrayHasKey('name', $vars);
		$this->assertArrayHasKey('lastname', $vars);
		$this->assertEquals('joe', $vars['name']);
		$this->assertEquals('doe', $vars['lastname']);
	}
	
	public function testArrayObjectParameterMap() {
		$value = new \ArrayObject(['name' => 'joe', 'lastname' => 'doe']);
		$wrapper = ParameterWrapper::wrapValue($value, 'Acme\Reflection\Parameter\UserArrayParameterMap');
		
		$this->assertInstanceOf('eMapper\Reflection\Parameter\ArrayParameterWrapper', $wrapper);
		$this->assertTrue($wrapper->offsetExists('name'));
		$this->assertTrue($wrapper->offsetExists('surname'));
		$this->assertFalse($wrapper->offsetExists('lastname'));
		$this->assertEquals('joe', $wrapper['name']);
		$this->assertEquals('doe', $wrapper['surname']);
	}
	
	public function testStdClass() {
		$value = new \stdClass();
		$value->name = 'joe';
		$value->lastname = 'doe';
		
		$wrapper = ParameterWrapper::wrapValue($value);
		
		$this->assertInstanceOf('eMapper\Reflection\Parameter\ArrayParameterWrapper', $wrapper);
		$this->assertTrue($wrapper->offsetExists('name'));
		$this->assertTrue($wrapper->offsetExists('lastname'));
		$this->assertEquals('joe', $wrapper['name']);
		$this->assertEquals('doe', $wrapper['lastname']);
		
		$vars = $wrapper->getValueAsArray();
		$this->assertInternalType('array', $vars);
		$this->assertArrayHasKey('name', $vars);
		$this->assertArrayHasKey('lastname', $vars);
		$this->assertEquals('joe', $vars['name']);
		$this->assertEquals('doe', $vars['lastname']);
	}
	
	public function testStdClassParameterMap() {
		$value = new \stdClass();
		$value->name = 'joe';
		$value->lastname = 'doe';
		
		$wrapper = ParameterWrapper::wrapValue($value, 'Acme\Reflection\Parameter\UserArrayParameterMap');
		
		$this->assertInstanceOf('eMapper\Reflection\Parameter\ArrayParameterWrapper', $wrapper);
		$this->assertTrue($wrapper->offsetExists('name'));
		$this->assertTrue($wrapper->offsetExists('surname'));
		$this->assertFalse($wrapper->offsetExists('lastname'));
		$this->assertEquals('joe', $wrapper['name']);
		$this->assertEquals('doe', $wrapper['surname']);
	}
	
	public function testObject() {
		$value = new ExampleUser('joe', 'doe', '123456');
		$wrapper = ParameterWrapper::wrapValue($value);
		
		$this->assertInstanceOf('eMapper\Reflection\Parameter\ObjectParameterWrapper', $wrapper);
		$this->assertTrue($wrapper->offsetExists('name'));
		$this->assertTrue($wrapper->offsetExists('lastname'));
		$this->assertTrue($wrapper->offsetExists('password'));
		$this->assertEquals('joe', $wrapper['name']);
		$this->assertEquals('doe', $wrapper['lastname']);
		
		$vars = $wrapper->getValueAsArray();
		$this->assertInternalType('array', $vars);
		$this->assertArrayHasKey('name', $vars);
		$this->assertArrayHasKey('lastname', $vars);
		$this->assertEquals('joe', $vars['name']);
		$this->assertEquals('doe', $vars['lastname']);
	}
	
	public function testObjectParameterMap() {
		$value = new ExampleUser('joe', 'doe', '123456');
		$wrapper = ParameterWrapper::wrapValue($value, 'Acme\Reflection\Parameter\ExampleUserPameterMap');
		
		$this->assertInstanceOf('eMapper\Reflection\Parameter\ObjectParameterWrapper', $wrapper);
		$this->assertTrue($wrapper->offsetExists('name'));
		$this->assertTrue($wrapper->offsetExists('surname'));
		$this->assertTrue($wrapper->offsetExists('pass'));
		$this->assertFalse($wrapper->offsetExists('lastname'));
		$this->assertFalse($wrapper->offsetExists('password'));
		$this->assertEquals('joe', $wrapper['name']);
		$this->assertEquals('doe', $wrapper['surname']);
		$this->assertEquals('123456', $wrapper['pass']);
	}
	
	public function testEntity() {
		$value = new UserEntity();
		$value->name = 'joe';
		$value->surname = 'doe';
		$value->setPassword('123456');
		
		$wrapper = ParameterWrapper::wrapValue($value);
		$this->assertInstanceOf('eMapper\Reflection\Parameter\ObjectParameterWrapper', $wrapper);
		$this->assertTrue($wrapper->offsetExists('name'));
		$this->assertTrue($wrapper->offsetExists('surname'));
		$this->assertTrue($wrapper->offsetExists('password'));
		$this->assertFalse($wrapper->offsetExists('lastname'));
		$this->assertEquals('joe', $wrapper['name']);
		$this->assertEquals('doe', $wrapper['surname']);
		$this->assertEquals('123456', $wrapper['password']);
	}
}
?>