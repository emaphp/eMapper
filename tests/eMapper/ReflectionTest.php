<?php
namespace eMapper;

use eMapper\Reflection\Profiler;

/**
 * 
 * @author emaphp
 * @group reflection
 */
class ReflectionTest extends \PHPUnit_Framework_TestCase {
	public function testTypeHandlerAnnotations() {
		$profile = Profiler::getClassAnnotations('Acme\\Type\\DummyTypeHandler');
		$this->assertNotNull($profile);
		$this->assertInstanceOf("Minime\Annotations\AnnotationsBag", $profile);
		$this->assertTrue($profile->has('unquoted'));
	}
	
	public function testResultMapAnnotations() {
		$profile = Profiler::getClassAnnotations('Acme\\Result\\UserResultMap');
		$this->assertNotNull($profile);
		$this->assertInstanceOf("Minime\Annotations\AnnotationsBag", $profile);
		$this->assertTrue($profile->has('defaultClass'));
		$this->assertEquals('stdClass', $profile->get('defaultClass'));
	}
	
	public function testResultMapProfile() {
		list($profile, $properties) = Profiler::getClassProfile('Acme\\Result\\UserResultMap');
		$this->assertNotNull($profile);
		$this->assertInstanceOf("Minime\Annotations\AnnotationsBag", $profile);
		$this->assertTrue($profile->has('defaultClass'));
		$this->assertEquals('stdClass', $profile->get('defaultClass'));
		
		$this->assertInternalType('array', $properties);
		
		$this->assertArrayHasKey('user_id', $properties);
		$this->assertInstanceOf("Minime\Annotations\AnnotationsBag", $properties['user_id']);
		$this->assertTrue($properties['user_id']->has('type'));
		$this->assertEquals('integer', $properties['user_id']->get('type'));
		
		$this->assertArrayHasKey('name', $properties);
		$this->assertInstanceOf("Minime\Annotations\AnnotationsBag", $properties['name']);
		$this->assertTrue($properties['name']->has('column'));
		$this->assertEquals('user_name', $properties['name']->get('column'));
		
		$this->assertArrayHasKey('lastLogin', $properties);
		$this->assertInstanceOf("Minime\Annotations\AnnotationsBag", $properties['lastLogin']);
		$this->assertTrue($properties['lastLogin']->has('type'));
		$this->assertEquals('string', $properties['lastLogin']->get('type'));
		$this->assertTrue($properties['lastLogin']->has('column'));
		$this->assertEquals('last_login', $properties['lastLogin']->get('column'));
	}
	
	public function testEntityAnnotations() {
		$profile = Profiler::getClassAnnotations('Acme\\Entity\\Product');
		$this->assertTrue($profile->has('entity'));
		
		$properties = Profiler::getClassProperties('Acme\\Entity\\Product');
		
		$this->assertArrayHasKey('code', $properties);
		$this->assertArrayHasKey('category', $properties);
		$this->assertArrayHasKey('color', $properties);
		
		$this->assertTrue($properties['code']->has('column'));
		$this->assertEquals('product_code', $properties['code']->get('column'));
		
		$this->assertTrue($properties['category']->has('setter'));
		$this->assertEquals('setCategory', $properties['category']->get('setter'));
		$this->assertTrue($properties['category']->has('getter'));
		$this->assertEquals('getCategory', $properties['category']->get('getter'));
		
		$this->assertTrue($properties['color']->has('type'));
		$this->assertEquals('Acme\\RGBColor', $properties['color']->get('type'));
	}
	
	public function testSubclass() {
		$profile = Profiler::getClassAnnotations('Acme\\Entity\\Car');
		$this->assertFalse($profile->has('moves'));
		$this->assertTrue($profile->has('color'));
		$this->assertEquals('red', $profile->get('color'));
		$this->assertTrue($profile->has('speed'));
		$this->assertEquals('fast', $profile->get('speed'));
		
		$properties = Profiler::getClassProperties('Acme\\Entity\\Car');
		$this->assertArrayHasKey('capacity', $properties);
		$this->assertArrayHasKey('wheels', $properties);
		$this->assertArrayHasKey('engine', $properties);
		
		$this->assertTrue($properties['capacity']->has('full'));
		$this->assertEquals(4, $properties['capacity']->get('full'));
		$this->assertFalse($properties['capacity']->has('measure'));

		$this->assertTrue($properties['engine']->has('requires'));
		$this->assertEquals('fuel', $properties['engine']->get('requires'));
	}
}
?>
