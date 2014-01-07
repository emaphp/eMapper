<?php
namespace eMapper;

use eMapper\Environment\Provider\EnvironmentProvider;

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
		$environment = EnvironmentProvider::getEnvironment('not_found');
	}
	
	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testProviderError3() {
		$environment = EnvironmentProvider::getEnvironment('eMapper\Engine\Generic\GenericMapper');
	}
	
	public function testProvider() {
		$env = EnvironmentProvider::getEnvironment('eMapper\Environment\DynamicSQLEnvironment');
		$this->assertInstanceOf('eMapper\Environment\DynamicSQLEnvironment', $env);
	}
}
?>