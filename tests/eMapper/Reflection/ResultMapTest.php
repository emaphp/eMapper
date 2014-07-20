<?php
namespace eMapper\Reflection;

use eMapper\Reflection\Profiler;
use Minime\Annotations\Facade;

/**
 * Tests parsing result map annotations through the Profiler class
 *  
 * @author emaphp
 * @group reflection
 */
class ResultMapTest extends \PHPUnit_Framework_TestCase {
	public function testTypeHandlerAnnotations() {
		$profile = Profiler::getClassProfile('Acme\\Type\\DummyTypeHandler')->classAnnotations;
		$this->assertNotNull($profile);
		$this->assertInstanceOf("Minime\Annotations\AnnotationsBag", $profile);
		$this->assertTrue($profile->has('Safe'));
	}
	
	public function testResultMapProfile() {		
		$profile = Profiler::getClassProfile('Acme\\Result\\UserResultMap')->classAnnotations;
		$properties = Profiler::getClassProfile('Acme\\Result\\UserResultMap')->propertiesConfig;
		
		$this->assertNotNull($profile);
		$this->assertInstanceOf("Minime\Annotations\AnnotationsBag", $profile);
		
		$this->assertInternalType('array', $properties);
		
		$this->assertArrayHasKey('user_id', $properties);
		$this->assertInstanceOf("Minime\Annotations\AnnotationsBag", $properties['user_id']);
		$this->assertTrue($properties['user_id']->has('Type'));
		$this->assertEquals('integer', $properties['user_id']->get('Type'));
		
		$this->assertArrayHasKey('name', $properties);
		$this->assertInstanceOf("Minime\Annotations\AnnotationsBag", $properties['name']);
		$this->assertTrue($properties['name']->has('Column'));
		$this->assertEquals('user_name', $properties['name']->get('Column'));
		
		$this->assertArrayHasKey('lastLogin', $properties);
		$this->assertInstanceOf("Minime\Annotations\AnnotationsBag", $properties['lastLogin']);
		$this->assertTrue($properties['lastLogin']->has('Type'));
		$this->assertEquals('string', $properties['lastLogin']->get('Type'));
		$this->assertTrue($properties['lastLogin']->has('Column'));
		$this->assertEquals('last_login', $properties['lastLogin']->get('Column'));
	}
	
	public function testEntityAnnotations() {
		$profile = Profiler::getClassProfile('Acme\\Entity\\Product')->classAnnotations;
		$this->assertTrue($profile->has('map.entity'));
		
		$properties = Profiler::getClassProfile('Acme\\Entity\\Product')->propertiesAnnotations;
		
		$this->assertArrayHasKey('code', $properties);
		$this->assertArrayHasKey('category', $properties);
		$this->assertArrayHasKey('color', $properties);
		
		$this->assertTrue($properties['code']->has('map.column'));
		$this->assertEquals('product_code', $properties['code']->get('Column'));

		$this->assertTrue($properties['color']->has('map.type'));
		$this->assertEquals('Acme\\RGBColor', $properties['color']->get('Type'));
	}
	
	public function testSubclass() {
		$profile = Profiler::getClassProfile('Acme\\Entity\\Car')->classAnnotations;
		$this->assertFalse($profile->has('moves'));
		$this->assertTrue($profile->has('color'));
		$this->assertEquals('red', $profile->get('color'));
		$this->assertTrue($profile->has('speed'));
		$this->assertEquals('fast', $profile->get('speed'));
		
		$properties = Facade::getAnnotations(Profiler::getClassProfile('Acme\\Entity\\Car')->propertiesConfig['capacity']->reflectionProperty);
		$this->assertTrue($properties->has('full'));
		$this->assertEquals(4, $properties->get('full'));
		$this->assertFalse($properties->has('measure'));

		$properties = Facade::getAnnotations(Profiler::getClassProfile('Acme\\Entity\\Car')->propertiesConfig['engine']->reflectionProperty);
		$this->assertTrue($properties->has('requires'));
		$this->assertEquals('fuel', $properties->get('requires'));
	}
	
	public function testRelationAnnotations() {
		$properties = Profiler::getClassProfile('Acme\Reflection\User')->dynamicAttributes;
		
		//full name
		$fullName = Facade::getAnnotations($properties['fullName']->reflectionProperty);
		$this->assertTrue($fullName->has('Eval'));
		$this->assertInternalType('string', $fullName->get('Eval'));
		$this->assertEquals("(. (#surname) ', ' (#name))", $fullName->get('Eval'));
		
		//profiles
		$profiles = Facade::getAnnotations($properties['profiles']->reflectionProperty);
		$this->assertTrue($profiles->has('StatementId'));
		$this->assertInternalType('string', $profiles->get('StatementId'));
		$this->assertEquals("profiles.findByUserId", $profiles->get('StatementId'));
		$this->assertTrue($profiles->has('Parameter'));
		$this->assertInternalType('array', $profiles->get('Parameter'));
		$this->assertCount(2, $profiles->get('Parameter'));
		$this->assertInternalType('string', $profiles->get('Parameter')[0]);
		$this->assertInternalType('integer', $profiles->get('Parameter')[1]);
		$this->assertEquals('#id', $profiles->get('Parameter')[0]);
		$this->assertEquals(3, $profiles->get('Parameter')[1]);
		
		//total profiles
		$totalProfiles = Facade::getAnnotations($properties['totalProfiles']->reflectionProperty);
		$this->assertTrue($totalProfiles->has('Eval'));
		$this->assertInternalType('string', $totalProfiles->get('Eval'));
		$this->assertEquals('(+ (count (#profiles)) (%0))', $totalProfiles->get('Eval'));
		$this->assertTrue($totalProfiles->has('Self'));
		$this->assertTrue($totalProfiles->has('Parameter'));
		$this->assertInternalType('integer', $totalProfiles->get('Parameter'));
		$this->assertEquals(1, $totalProfiles->get('map.arg'));
		
		//last connection
		$lastConnection = Facade::getAnnotations($properties['lastConnection']->reflectionProperty);
		$this->assertTrue($lastConnection->has('Query'));
		$this->assertInternalType('string', $lastConnection->get('Query'));
		$this->assertEquals("SELECT last_login FROM login WHERE user_id = %{i}", $lastConnection->get('Query'));
		$this->assertTrue($lastConnection->has('Parameter'));
		$this->assertInternalType('string', $lastConnection->get('Parameter'));
		$this->assertEquals('#id', $lastConnection->get('Parameter'));
		$this->assertTrue($lastConnection->has('Type'));
		$this->assertEquals('dt', $lastConnection->get('Type'));
		
		//favorites
		$favorites = Facade::getAnnotations($properties['favorites']->reflectionProperty);
		$this->assertTrue($favorites->has('Query'));
		$this->assertInternalType('string', $favorites->get('Query'));
		$this->assertEquals("SELECT link FROM favorites WHERE user_id = #{id} AND confirmed = %{bool}", $favorites->get('Query'));
		$this->assertTrue($favorites->has('Self'));
		$this->assertTrue($favorites->has('Parameter'));
		$this->assertInternalType('boolean', $favorites->get('Parameter'));
		$this->assertTrue($favorites->has('Type'));
		$this->assertEquals('string[]', $favorites->get('Type'));
	}
}
?>
