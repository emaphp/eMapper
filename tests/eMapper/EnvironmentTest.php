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
		$environment = EnvironmentProvider::getEnvironment(1);
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testProviderError2() {
		EnvironmentProvider::buildEnvironment('fake', 'eMapper\Engine\Generic\GenericMapper', null);
	}
	
	public function testProvider() {
		EnvironmentProvider::buildEnvironment('default', 'eMapper\Dynamic\Environment\DynamicSQLEnvironment', null);
		$this->assertTrue(EnvironmentProvider::hasEnvironment('default'));
		$env = EnvironmentProvider::getEnvironment('default');
		$this->assertInstanceOf('eMapper\Dynamic\Environment\DynamicSQLEnvironment', $env);
		$this->assertTrue($env->hasPackage('Core'));
		$this->assertTrue($env->hasPackage('Date'));
	}
	
	public function testProvider2() {
		EnvironmentProvider::buildEnvironment('custom', 'eMacros\Environment\Environment', array('eMacros\Package\CorePackage', 'eMacros\Package\StringPackage'));
		$env = EnvironmentProvider::getEnvironment('custom');
		$this->assertInstanceOf('eMacros\Environment\Environment', $env);
		$this->assertTrue($env->hasPackage('Core'));
		$this->assertTrue($env->hasPackage('String'));
	}
}
?>