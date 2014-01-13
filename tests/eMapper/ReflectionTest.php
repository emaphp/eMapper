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
	
	public function testRelationAnnotations() {
		$properties = Profiler::getClassProperties('Acme\Reflection\User');
		
		//full name
		$fullName = $properties['fullName'];
		$this->assertTrue($fullName->has('eval'));
		$this->assertInternalType('string', $fullName->get('eval'));
		$this->assertEquals("(. (#surname) ', ' (#name))", $fullName->get('eval'));
		$this->assertTrue($fullName->has('var'));
		$this->assertInternalType('string', $fullName->get('var'));
		$this->assertEquals('string', $fullName->get('var'));
		
		//profiles
		$profiles = $properties['profiles'];
		$this->assertTrue($profiles->has('stmt'));
		$this->assertInternalType('string', $profiles->get('stmt'));
		$this->assertEquals("profiles.findByUserId", $profiles->get('stmt'));
		$this->assertTrue($profiles->has('arg'));
		$this->assertInternalType('array', $profiles->get('arg'));
		$this->assertCount(2, $profiles->get('arg'));
		$this->assertInternalType('string', $profiles->get('arg')[0]);
		$this->assertInternalType('integer', $profiles->get('arg')[1]);
		$this->assertEquals('#id', $profiles->get('arg')[0]);
		$this->assertEquals(3, $profiles->get('arg')[1]);
		
		//total profiles
		$totalProfiles = $properties['totalProfiles'];
		$this->assertTrue($totalProfiles->has('eval'));
		$this->assertInternalType('string', $totalProfiles->get('eval'));
		$this->assertEquals('(+ (count (#profiles)) (%0))', $totalProfiles->get('eval'));
		$this->assertTrue($totalProfiles->has('arg-self'));
		$this->assertTrue($totalProfiles->has('arg'));
		$this->assertInternalType('integer', $totalProfiles->get('arg'));
		$this->assertEquals(1, $totalProfiles->get('arg'));
		
		//last connection
		$lastConnection = $properties['lastConnection'];
		$this->assertTrue($lastConnection->has('query'));
		$this->assertInternalType('string', $lastConnection->get('query'));
		$this->assertEquals("SELECT last_login FROM login WHERE user_id = %{i}", $lastConnection->get('query'));
		$this->assertTrue($lastConnection->has('arg'));
		$this->assertInternalType('string', $lastConnection->get('arg'));
		$this->assertEquals('#id', $lastConnection->get('arg'));
		$this->assertTrue($lastConnection->has('option'));
		$this->assertInternalType('array', $lastConnection->get('option'));
		$this->assertInternalType('string', $lastConnection->get('option')[0]);
		$this->assertInternalType('string', $lastConnection->get('option')[1]);
		$this->assertEquals('map.type dt', $lastConnection->get('option')[0]);
		$this->assertEquals('custom 100', $lastConnection->get('option')[1]);
		
		//favorites
		$favorites = $properties['favorites'];
		$this->assertTrue($favorites->has('query'));
		$this->assertInternalType('string', $favorites->get('query'));
		$this->assertEquals("SELECT link FROM favorites WHERE user_id = #{id} AND confirmed = %{bool}", $favorites->get('query'));
		$this->assertTrue($favorites->has('arg-self'));
		$this->assertTrue($favorites->has('arg'));
		$this->assertInternalType('boolean', $favorites->get('arg'));
		$this->assertTrue($favorites->has('option'));
		$this->assertInternalType('string', $favorites->get('option'));
		$this->assertEquals('map.type string[]', $favorites->get('option'));
	}
	
}
?>
