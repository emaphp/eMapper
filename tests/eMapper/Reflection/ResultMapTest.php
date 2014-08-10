<?php
namespace eMapper\Reflection;

use eMapper\Reflection\Profiler;
use eMapper\Annotations\Facade;

/**
 * Tests parsing result map annotations through the Profiler class
 *  
 * @author emaphp
 * @group reflection
 */
class ResultMapTest extends \PHPUnit_Framework_TestCase {
	public function testTypeHandlerAnnotations() {
		$profile = Profiler::getClassProfile('Acme\\Type\\DummyTypeHandler')->getClassAnnotations();
		$this->assertNotNull($profile);
		$this->assertInstanceOf("eMapper\Annotations\AnnotationsBag", $profile);
		$this->assertTrue($profile->has('Safe'));
	}
	
	public function testResultMapProfile() {		
		$profile = Profiler::getClassProfile('Acme\\Result\\UserResultMap')->getClassAnnotations();
		$this->assertNotNull($profile);
		$this->assertInstanceOf("eMapper\Annotations\AnnotationsBag", $profile);
		
		$properties = Profiler::getClassProfile('Acme\\Result\\UserResultMap')->getProperties();
		
		$this->assertArrayHasKey('user_id', $properties);
		$annotations = Facade::getAnnotations($properties['user_id']->getReflectionProperty());
		$this->assertTrue($annotations->has('Type'));
		$this->assertEquals('integer', $annotations->get('Type')->getValue());
		
		$this->assertArrayHasKey('name', $properties);
		$annotations = Facade::getAnnotations($properties['name']->getReflectionProperty());
		$this->assertTrue($annotations->has('Column'));
		$this->assertEquals('user_name', $annotations->get('Column')->getValue());
		
		$this->assertArrayHasKey('lastLogin', $properties);
		$annotations = Facade::getAnnotations($properties['lastLogin']->getReflectionProperty());
		$this->assertTrue($annotations->has('Type'));
		$this->assertEquals('string', $annotations->get('Type')->getValue());
		$this->assertTrue($annotations->has('Column'));
		$this->assertEquals('last_login', $annotations->get('Column')->getValue());
	}
	
	public function testEntityAnnotations() {
		$profile = Profiler::getClassProfile('Acme\\Entity\\Product')->getClassAnnotations();
		$this->assertTrue($profile->has('Entity'));
		
		$properties = Profiler::getClassProfile('Acme\\Entity\\Product')->getProperties();
		
		$this->assertArrayHasKey('code', $properties);
		$this->assertArrayHasKey('category', $properties);
		$this->assertArrayHasKey('color', $properties);
		
		$annotations = Facade::getAnnotations($properties['code']->getReflectionProperty());
		$this->assertTrue($annotations->has('Column'));
		$this->assertEquals('product_code', $annotations->get('Column')->getValue());

		$annotations = Facade::getAnnotations($properties['color']->getReflectionProperty());
		$this->assertTrue($annotations->has('Type'));
		$this->assertEquals('Acme\\RGBColor', $annotations->get('Type')->getValue());
	}
	
	public function testSubclass() {
		$profile = Profiler::getClassProfile('Acme\\Entity\\Car')->getClassAnnotations();
		$this->assertFalse($profile->has('moves'));
		$this->assertTrue($profile->has('color'));
		$this->assertEquals('red', $profile->get('color')->getValue());
		$this->assertTrue($profile->has('speed'));
		$this->assertEquals('fast', $profile->get('speed')->getValue());
		
		$properties = Facade::getAnnotations(Profiler::getClassProfile('Acme\\Entity\\Car')->getProperty('capacity')->getReflectionProperty());
		$this->assertTrue($properties->has('full'));
		$this->assertEquals(4, $properties->get('full')->getValue());
		$this->assertFalse($properties->has('measure'));

		$properties = Facade::getAnnotations(Profiler::getClassProfile('Acme\\Entity\\Car')->getProperty('engine')->getReflectionProperty());
		$this->assertTrue($properties->has('requires'));
		$this->assertEquals('fuel', $properties->get('requires')->getValue());
	}
	
	public function testRelationAnnotations() {
		$firstOrderAttributes = Profiler::getClassProfile('Acme\Reflection\User')->getFirstOrderAttributes();
		$secondOrderAttributes = Profiler::getClassProfile('Acme\Reflection\User')->getSecondOrderAttributes();
		
		//full name
		$fullName = Facade::getAnnotations($firstOrderAttributes['fullName']->getReflectionProperty());
		$this->assertTrue($fullName->has('Eval'));
		$this->assertInternalType('string', $fullName->get('Eval')->getValue());
		$this->assertEquals("(. (#surname) ', ' (#name))", $fullName->get('Eval')->getValue());
		
		//profiles
		$profiles = Facade::getAnnotations($secondOrderAttributes['profiles']->getReflectionProperty());
		$this->assertTrue($profiles->has('StatementId'));
		$this->assertInternalType('string', $profiles->get('StatementId')->getValue());
		$this->assertEquals("profiles.findByUserId", $profiles->get('StatementId')->getValue());
		$this->assertTrue($profiles->has('Parameter'));
		$this->assertInternalType('boolean', $profiles->get('Parameter')->getValue());
		$this->assertEquals(true, $profiles->get('Parameter')->getValue());
		$this->assertInternalType('boolean', $profiles->find('Parameter')[0]->getValue());
		$this->assertInternalType('integer', $profiles->find('Parameter')[1]->getValue());
		$this->assertEquals('id', $profiles->find('Parameter')[0]->getArgument());
		$this->assertEquals(3, $profiles->find('Parameter')[1]->getValue());
		
		//total profiles
		$totalProfiles = Facade::getAnnotations($firstOrderAttributes['totalProfiles']->getReflectionProperty());
		$this->assertTrue($totalProfiles->has('Eval'));
		$this->assertInternalType('string', $totalProfiles->get('Eval')->getValue());
		$this->assertEquals('(+ (count (#profiles)) (%0))', $totalProfiles->get('Eval')->getValue());
		$this->assertTrue($totalProfiles->has('Self'));
		$this->assertTrue($totalProfiles->has('Parameter'));
		$this->assertInternalType('integer', $totalProfiles->get('Parameter')->getValue());
		
		//last connection
		$lastConnection = Facade::getAnnotations($firstOrderAttributes['lastConnection']->getReflectionProperty());
		$this->assertTrue($lastConnection->has('Query'));
		$this->assertInternalType('string', $lastConnection->get('Query')->getValue());
		$this->assertEquals("SELECT last_login FROM login WHERE user_id = %{i}", $lastConnection->get('Query')->getValue());
		$this->assertTrue($lastConnection->has('Parameter'));
		$this->assertInternalType('boolean', $lastConnection->get('Parameter')->getValue());
		$this->assertEquals('id', $lastConnection->get('Parameter')->getArgument());
		$this->assertTrue($lastConnection->has('Type'));
		$this->assertEquals('dt', $lastConnection->get('Type')->getValue());
		
		//favorites
		$favorites = Facade::getAnnotations($secondOrderAttributes['favorites']->getReflectionProperty());
		$this->assertTrue($favorites->has('Query'));
		$this->assertInternalType('string', $favorites->get('Query')->getValue());
		$this->assertEquals("SELECT link FROM favorites WHERE user_id = #{id} AND confirmed = %{bool}", $favorites->get('Query')->getValue());
		$this->assertTrue($favorites->has('Self'));
		$this->assertTrue($favorites->has('Parameter'));
		$this->assertInternalType('boolean', $favorites->get('Parameter')->getValue());
		$this->assertTrue($favorites->has('Type'));
		$this->assertEquals('string[]', $favorites->get('Type')->getValue());
	}
}
?>
