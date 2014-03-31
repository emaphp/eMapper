<?php
namespace eMapper\Reflection;

use eMapper\Reflection\Profiler;

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
		$this->assertTrue($profile->has('map.unquoted'));
	}
	
	public function testResultMapProfile() {		
		$profile = Profiler::getClassProfile('Acme\\Result\\UserResultMap')->classAnnotations;
		$properties = Profiler::getClassProfile('Acme\\Result\\UserResultMap')->propertiesAnnotations;
		
		$this->assertNotNull($profile);
		$this->assertInstanceOf("Minime\Annotations\AnnotationsBag", $profile);
		
		$this->assertInternalType('array', $properties);
		
		$this->assertArrayHasKey('user_id', $properties);
		$this->assertInstanceOf("Minime\Annotations\AnnotationsBag", $properties['user_id']);
		$this->assertTrue($properties['user_id']->has('map.type'));
		$this->assertEquals('integer', $properties['user_id']->get('map.type'));
		
		$this->assertArrayHasKey('name', $properties);
		$this->assertInstanceOf("Minime\Annotations\AnnotationsBag", $properties['name']);
		$this->assertTrue($properties['name']->has('map.column'));
		$this->assertEquals('user_name', $properties['name']->get('map.column'));
		
		$this->assertArrayHasKey('lastLogin', $properties);
		$this->assertInstanceOf("Minime\Annotations\AnnotationsBag", $properties['lastLogin']);
		$this->assertTrue($properties['lastLogin']->has('map.type'));
		$this->assertEquals('string', $properties['lastLogin']->get('map.type'));
		$this->assertTrue($properties['lastLogin']->has('map.column'));
		$this->assertEquals('last_login', $properties['lastLogin']->get('map.column'));
	}
	
	public function testEntityAnnotations() {
		$profile = Profiler::getClassProfile('Acme\\Entity\\Product')->classAnnotations;
		$this->assertTrue($profile->has('map.entity'));
		
		$properties = Profiler::getClassProfile('Acme\\Entity\\Product')->propertiesAnnotations;
		
		$this->assertArrayHasKey('code', $properties);
		$this->assertArrayHasKey('category', $properties);
		$this->assertArrayHasKey('color', $properties);
		
		$this->assertTrue($properties['code']->has('map.column'));
		$this->assertEquals('product_code', $properties['code']->get('map.column'));

		$this->assertTrue($properties['color']->has('map.type'));
		$this->assertEquals('Acme\\RGBColor', $properties['color']->get('map.type'));
	}
	
	public function testSubclass() {
		$profile = Profiler::getClassProfile('Acme\\Entity\\Car')->classAnnotations;
		$this->assertFalse($profile->has('moves'));
		$this->assertTrue($profile->has('color'));
		$this->assertEquals('red', $profile->get('color'));
		$this->assertTrue($profile->has('speed'));
		$this->assertEquals('fast', $profile->get('speed'));
		
		$properties = Profiler::getClassProfile('Acme\\Entity\\Car')->propertiesAnnotations;
		$this->assertArrayHasKey('capacity', $properties);
		$this->assertArrayHasKey('wheels', $properties);
		$this->assertArrayHasKey('engine', $properties);
		
		$this->assertTrue($properties['capacity']->has('full'));
		$this->assertEquals(4, $properties['capacity']->get('full'));
		$this->assertFalse($properties['capacity']->has('measure'));

		$this->assertTrue($properties['engine']->has('requires'));
		$this->assertEquals('fuel', $properties['engine']->get('requires'));
	}
	
	public function testRelationAnnotations() {
		$properties = Profiler::getClassProfile('Acme\Reflection\User')->propertiesAnnotations;
		
		//full name
		$fullName = $properties['fullName'];
		$this->assertTrue($fullName->has('map.eval'));
		$this->assertInternalType('string', $fullName->get('map.eval'));
		$this->assertEquals("(. (#surname) ', ' (#name))", $fullName->get('map.eval'));
		$this->assertTrue($fullName->has('var'));
		$this->assertInternalType('string', $fullName->get('var'));
		$this->assertEquals('string', $fullName->get('var'));
		
		//profiles
		$profiles = $properties['profiles'];
		$this->assertTrue($profiles->has('map.stmt'));
		$this->assertInternalType('string', $profiles->get('map.stmt'));
		$this->assertEquals("profiles.findByUserId", $profiles->get('map.stmt'));
		$this->assertTrue($profiles->has('map.arg'));
		$this->assertInternalType('array', $profiles->get('map.arg'));
		$this->assertCount(2, $profiles->get('map.arg'));
		$this->assertInternalType('string', $profiles->get('map.arg')[0]);
		$this->assertInternalType('integer', $profiles->get('map.arg')[1]);
		$this->assertEquals('#id', $profiles->get('map.arg')[0]);
		$this->assertEquals(3, $profiles->get('map.arg')[1]);
		
		//total profiles
		$totalProfiles = $properties['totalProfiles'];
		$this->assertTrue($totalProfiles->has('map.eval'));
		$this->assertInternalType('string', $totalProfiles->get('map.eval'));
		$this->assertEquals('(+ (count (#profiles)) (%0))', $totalProfiles->get('map.eval'));
		$this->assertTrue($totalProfiles->has('map.self-arg'));
		$this->assertTrue($totalProfiles->has('map.arg'));
		$this->assertInternalType('integer', $totalProfiles->get('map.arg'));
		$this->assertEquals(1, $totalProfiles->get('map.arg'));
		
		//last connection
		$lastConnection = $properties['lastConnection'];
		$this->assertTrue($lastConnection->has('map.query'));
		$this->assertInternalType('string', $lastConnection->get('map.query'));
		$this->assertEquals("SELECT last_login FROM login WHERE user_id = %{i}", $lastConnection->get('map.query'));
		$this->assertTrue($lastConnection->has('map.arg'));
		$this->assertInternalType('string', $lastConnection->get('map.arg'));
		$this->assertEquals('#id', $lastConnection->get('map.arg'));
		$this->assertTrue($lastConnection->has('map.type'));
		$this->assertEquals('dt', $lastConnection->get('map.type'));
		
		//favorites
		$favorites = $properties['favorites'];
		$this->assertTrue($favorites->has('map.query'));
		$this->assertInternalType('string', $favorites->get('map.query'));
		$this->assertEquals("SELECT link FROM favorites WHERE user_id = #{id} AND confirmed = %{bool}", $favorites->get('map.query'));
		$this->assertTrue($favorites->has('map.self-arg'));
		$this->assertTrue($favorites->has('map.arg'));
		$this->assertInternalType('boolean', $favorites->get('map.arg'));
		$this->assertTrue($favorites->has('map.type'));
		$this->assertEquals('string[]', $favorites->get('map.type'));
	}
}
?>
