<?php
namespace eMapper\Reflection;

use eMapper\Reflection\Profiler;
use Omocha\Omocha;

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
		$this->assertInstanceOf("Omocha\AnnotationBag", $profile);
		$this->assertTrue($profile->has('Safe'));
	}
	
	public function testResultMapProfile() {		
		$profile = Profiler::getClassProfile('Acme\\Result\\UserResultMap')->getClassAnnotations();
		$this->assertNotNull($profile);
		$this->assertInstanceOf("Omocha\AnnotationBag", $profile);
		
		$properties = Profiler::getClassProfile('Acme\\Result\\UserResultMap')->getProperties();
		
		$this->assertArrayHasKey('user_id', $properties);
		$annotations = Omocha::getAnnotations($properties['user_id']->getReflectionProperty());
		$this->assertTrue($annotations->has('Type'));
		$this->assertEquals('integer', $annotations->get('Type')->getValue());
		
		$this->assertArrayHasKey('name', $properties);
		$annotations = Omocha::getAnnotations($properties['name']->getReflectionProperty());
		$this->assertTrue($annotations->has('Column'));
		$this->assertEquals('user_name', $annotations->get('Column')->getValue());
		
		$this->assertArrayHasKey('lastLogin', $properties);
		$annotations = Omocha::getAnnotations($properties['lastLogin']->getReflectionProperty());
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
		
		$annotations = Omocha::getAnnotations($properties['code']->getReflectionProperty());
		$this->assertTrue($annotations->has('Column'));
		$this->assertEquals('product_code', $annotations->get('Column')->getValue());

		$annotations = Omocha::getAnnotations($properties['color']->getReflectionProperty());
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
		
		$properties = Omocha::getAnnotations(Profiler::getClassProfile('Acme\\Entity\\Car')->getProperty('capacity')->getReflectionProperty());
		$this->assertTrue($properties->has('full'));
		$this->assertEquals(4, $properties->get('full')->getValue());
		$this->assertFalse($properties->has('measure'));

		$properties = Omocha::getAnnotations(Profiler::getClassProfile('Acme\\Entity\\Car')->getProperty('engine')->getReflectionProperty());
		$this->assertTrue($properties->has('requires'));
		$this->assertEquals('fuel', $properties->get('requires')->getValue());
	}
	
	public function testRelationAnnotations() {
		$firstOrderAttributes = Profiler::getClassProfile('Acme\Reflection\User')->getFirstOrderAttributes();
		$secondOrderAttributes = Profiler::getClassProfile('Acme\Reflection\User')->getSecondOrderAttributes();
		
		//full name
		$fullName = Omocha::getAnnotations($firstOrderAttributes['fullName']->getReflectionProperty());
		$this->assertTrue($fullName->has('Eval'));
		$this->assertInternalType('string', $fullName->get('Eval')->getValue());
		$this->assertEquals("(. (#surname) ', ' (#name))", $fullName->get('Eval')->getValue());
		
		//profiles
		$profiles = Omocha::getAnnotations($secondOrderAttributes['profiles']->getReflectionProperty());
		$this->assertTrue($profiles->has('Statement'));
		$this->assertInternalType('string', $profiles->get('Statement')->getValue());
		$this->assertEquals("Profile.findByUserId", $profiles->get('Statement')->getValue());
		$this->assertTrue($profiles->has('Param'));
		$this->assertInternalType('boolean', $profiles->get('Param')->getValue());
		$this->assertEquals(true, $profiles->get('Param')->getValue());
		$this->assertInternalType('boolean', $profiles->find('Param')[0]->getValue());
		$this->assertEquals('id', $profiles->find('Param')[0]->getArgument());
		
		//total profiles
		$totalProfiles = Omocha::getAnnotations($firstOrderAttributes['totalProfiles']->getReflectionProperty());
		$this->assertTrue($totalProfiles->has('Eval'));
		$this->assertInternalType('string', $totalProfiles->get('Eval')->getValue());
		$this->assertEquals('(+ (count (#profiles)) (%0))', $totalProfiles->get('Eval')->getValue());
		$this->assertTrue($totalProfiles->has('Param'));
		$this->assertInternalType('boolean', $totalProfiles->find('Param')[0]->getValue());
		$this->assertInternalType('integer', $totalProfiles->find('Param')[1]->getValue());
		$this->assertEquals('self', $totalProfiles->find('Param')[0]->getArgument());
		$this->assertEquals(1, $totalProfiles->find('Param')[1]->getValue());
		
		//last connection
		$lastConnection = Omocha::getAnnotations($firstOrderAttributes['lastConnection']->getReflectionProperty());
		$this->assertTrue($lastConnection->has('Query'));
		$this->assertInternalType('string', $lastConnection->get('Query')->getValue());
		$this->assertEquals("SELECT last_login FROM login WHERE user_id = %{i}", $lastConnection->get('Query')->getValue());
		$this->assertTrue($lastConnection->has('Param'));
		$this->assertInternalType('boolean', $lastConnection->get('Param')->getValue());
		$this->assertEquals('id', $lastConnection->get('Param')->getArgument());
		$this->assertTrue($lastConnection->has('Type'));
		$this->assertEquals('dt', $lastConnection->get('Type')->getValue());
		
		//favorites
		$favorites = Omocha::getAnnotations($secondOrderAttributes['favorites']->getReflectionProperty());
		$this->assertTrue($favorites->has('Query'));
		$this->assertInternalType('string', $favorites->get('Query')->getValue());
		$this->assertEquals("SELECT link FROM favorites WHERE user_id = #{id} AND confirmed = %{bool}", $favorites->get('Query')->getValue());
		$this->assertTrue($favorites->has('Param'));
		$this->assertInternalType('boolean', $favorites->find('Param')[0]->getValue());
		$this->assertInternalType('boolean', $favorites->find('Param')[1]->getValue());
		$this->assertTrue($favorites->has('Type'));
		$this->assertEquals('string[]', $favorites->get('Type')->getValue());
	}
}
?>
