<?php
namespace eMapper;

use Acme\Configuration\ConfigurationContainer;

/**
 * Tests setting configuration values through the Configuration trait
 * 
 * @author emaphp
 * @group config
 */
class ConfigurationTest extends \PHPUnit_Framework_TestCase {
	public function testMerge() {
		$cc = new ConfigurationContainer();
		$cc->set('property', 'value');
		$cc->set('property2', 'value2');
		$cd = $cc->merge(array('property2' => 'value3'));
		$this->assertEquals(array('property' => 'value', 'property2' => 'value3'), $cd->config);
		
		$cc = new ConfigurationContainer();
		$cc->set('property', 'value');
		$cc->set('property2', 'value2');
		$cd = $cc->merge(array('property3' => 'value3'));
		$this->assertEquals(array('property' => 'value', 'property3' => 'value3', 'property2' => 'value2'), $cd->config);
		
		$cc = new ConfigurationContainer();
		$cc->set('property', 'value');
		$cc->set('property2', 'value2');
		$cd = $cc->merge(array('property2' => 'value3'), true);
		$this->assertEquals(array('property' => 'value', 'property2' => 'value2'), $cd->config);
		
		$cc = new ConfigurationContainer();
		$cc->set('property', 'value');
		$cc->set('property2', 'value2');
		$cd = $cc->merge(array('property3' => 'value3'), true);
		$this->assertEquals(array('property' => 'value', 'property2' => 'value2', 'property3' => 'value3'), $cd->config);
	}
	
	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testMergeError() {
		$cc = new ConfigurationContainer();
		$cd = $cc->merge(null);
	}
	
	public function testDiscard() {
		$cc = new ConfigurationContainer();
		$cc->set('property', 'value');
		$cc->set('property2', 'value2');
		$cd = $cc->discard('property');
		$this->assertEquals(array('property2' => 'value2'), $cd->config);
		
		$cc = new ConfigurationContainer();
		$cc->set('property', 'value');
		$cc->set('property2', 'value2');
		$cd = $cc->discard('property3');
		$this->assertEquals(array('property' => 'value', 'property2' => 'value2'), $cd->config);
		
		$cc = new ConfigurationContainer();
		$cc->set('property', 'value');
		$cc->set('property2', 'value2');
		$cd = $cc->discard('property', 'property2');
		$this->assertEquals(array(), $cd->config);
	}
	
	public function testOption() {
		$cc = new ConfigurationContainer();
		$cc->set('property', 'value');
		$cc->set('property2', 'value2');
		$cd = $cc->option('property2', 'value3');
		$this->assertEquals(array('property' => 'value', 'property2' => 'value3'), $cd->config);
		
		$cc = new ConfigurationContainer();
		$cc->set('property', 'value');
		$cc->set('property2', 'value2');
		$cd = $cc->option('property3', 'value3');
		$this->assertEquals(array('property' => 'value', 'property2' => 'value2', 'property3' => 'value3'), $cd->config);
		
		$cc = new ConfigurationContainer();
		$cc->set('property', 'value');
		$cc->set('property2', 'value2');
		$cd = $cc->option('property3', 'value3')->option('property3', 'value4');
		$this->assertEquals(array('property' => 'value', 'property2' => 'value2', 'property3' => 'value4'), $cd->config);
	}
	
	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testOptionError() {
		$cc = new ConfigurationContainer();
		$cc->option(null, null);
	}
	
	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testSetError() {
		$cc = new ConfigurationContainer();
		$cc->set(null, null);
	}
	
	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testGetError() {
		$cc = new ConfigurationContainer();
		$cc->get(null, null);
	}
}
?>