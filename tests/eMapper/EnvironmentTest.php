<?php
namespace eMapper;

use eMapper\Dynamic\Provider\EnvironmentProvider;

/**
 * 
 * @author emaphp
 * @group environment
 */
class EnvironmentTest extends \PHPUnit_Framework_TestCase {
	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testProviderError1() {
		$environment = EnvironmentProvider::getEnvironment(1, 'eMapper\Engine\Generic\GenericMapper', null);
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testProviderError2() {
		$environment = EnvironmentProvider::getEnvironment('fake', 'eMapper\Engine\Generic\GenericMapper', null);
	}
	
	public function testProvider() {
		$env = EnvironmentProvider::getEnvironment('default', 'eMapper\Dynamic\Environment\DynamicSQLEnvironment', null);
		$this->assertTrue(EnvironmentProvider::hasEnvironment('default'));
		$this->assertInstanceOf('eMapper\Dynamic\Environment\DynamicSQLEnvironment', $env);
		$this->assertTrue($env->hasPackage('Core'));
		$this->assertTrue($env->hasPackage('Date'));
	}
	
	public function testProvider2() {
		$env = EnvironmentProvider::getEnvironment('custom', 'eMacros\Environment\Environment', array('eMacros\Package\CorePackage', 'eMacros\Package\StringPackage'));
		$this->assertInstanceOf('eMacros\Environment\Environment', $env);
		$this->assertTrue($env->hasPackage('Core'));
		$this->assertTrue($env->hasPackage('String'));
	}
}
?>