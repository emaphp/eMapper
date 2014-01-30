<?php
namespace eMapper;

use eMapper\Statement\Statement;
use Acme\Result\UserResultMap;
use Acme\Parameter\ProductParameterMap;
use eMapper\Statement\StatementNamespace;

/**
 * Tests creating Statement and StatementNamespace instances and applying different configuration
 * 
 * @author emaphp
 * @group statement
 */
class StatementTest extends \PHPUnit_Framework_TestCase {
	public function testConfigType() {
		$config = Statement::config(array('map.type' => 'i'));
		$this->assertInstanceOf('\eMapper\Statement\Configuration\StatementConfigurationContainer', $config);
		$this->assertEquals(array('map.type' => 'i'), $config->config);
		
		$config = Statement::config()->type('i');
		$this->assertInstanceOf('\eMapper\Statement\Configuration\StatementConfigurationContainer', $config);
		$this->assertEquals(array('map.type' => 'i'), $config->config);
		
		$config = Statement::type('i');
		$this->assertInstanceOf('\eMapper\Statement\Configuration\StatementConfigurationContainer', $config);
		$this->assertEquals(array('map.type' => 'i'), $config->config);
	}
	
	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testConfigResultMap0() {
		$config = Statement::config()->result_map(null);
	}
	
	public function testConfigResultMap1() {
		$config = Statement::config()->result_map('Acme\Result\UserResultMap');
		$this->assertInstanceOf('\eMapper\Statement\Configuration\StatementConfigurationContainer', $config);
		$this->assertEquals(array('map.result' => 'Acme\Result\UserResultMap'), $config->config);
		
		$config = Statement::config()->result_map(new UserResultMap());
		$this->assertInstanceOf('\eMapper\Statement\Configuration\StatementConfigurationContainer', $config);
		$this->assertEquals(array('map.result' => 'Acme\Result\UserResultMap'), $config->config);
	}
	
	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testConfigParameterMap0() {
		$config = Statement::config()->parameter_map(null);
	}
	
	public function testConfigParameterMap1() {
		$config = Statement::config()->parameter_map('Acme\Parameter\ProductParameterMap');
		$this->assertInstanceOf('\eMapper\Statement\Configuration\StatementConfigurationContainer', $config);
		$this->assertEquals(array('map.parameter' => 'Acme\Parameter\ProductParameterMap'), $config->config);
		
		$config = Statement::config()->parameter_map(new ProductParameterMap());
		$this->assertInstanceOf('\eMapper\Statement\Configuration\StatementConfigurationContainer', $config);
		$this->assertEquals(array('map.parameter' => 'Acme\Parameter\ProductParameterMap'), $config->config);
	}
	
	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testConfigEach0() {
		$config = Statement::config()->each(0);
	}
	
	public function testConfigEach1() {
		$config = Statement::config()->each(function() {});
		$this->assertArrayHasKey('callback.each', $config->config);
		$this->assertInstanceOf('Closure', $config->config['callback.each']);
	}
	
	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testConfigFilter0() {
		$config = Statement::config()->filter(0);
	}
	
	public function testConfigFilter1() {
		$config = Statement::config()->filter(function() {});
		$this->assertArrayHasKey('callback.filter', $config->config);
		$this->assertInstanceOf('Closure', $config->config['callback.filter']);
	}
	
	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testConfigNoRows0() {
		$config = Statement::config()->no_rows(0);
	}
	
	public function testConfigNoRows1() {
		$config = Statement::config()->no_rows(function() {});
		$this->assertArrayHasKey('callback.no_rows', $config->config);
		$this->assertInstanceOf('Closure', $config->config['callback.no_rows']);
	}
	
	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testConfigQueryOverride0() {
		$config = Statement::config()->query_override(0);
	}
	
	public function testConfigQueryOverride1() {
		$config = Statement::config()->query_override(function() {});
		$this->assertArrayHasKey('callback.query', $config->config);
		$this->assertInstanceOf('Closure', $config->config['callback.query']);
	}
		
	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testConfigCache0() {
		$config = Statement::config()->cache(0);
	}
	
	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testConfigCache1() {
		$config = Statement::config()->cache('');
	}
	
	public function testConfigCache2() {
		$config = Statement::config()->cache('USERS');
		$this->assertArrayHasKey('cache.key', $config->config);
		$this->assertArrayHasKey('cache.ttl', $config->config);
		$this->assertEquals('USERS', $config->config['cache.key']);
		$this->assertEquals(0, $config->config['cache.ttl']);
		
		$config = Statement::config()->cache('USERS', 120);
		$this->assertArrayHasKey('cache.key', $config->config);
		$this->assertArrayHasKey('cache.ttl', $config->config);
		$this->assertEquals('USERS', $config->config['cache.key']);
		$this->assertEquals(120, $config->config['cache.ttl']);
	}
	
	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testCreate0() {
		$stmt = new Statement('*');
	}
	
	public function testCreate1() {
		$stmt = new Statement('findAll');
		$this->assertEquals('', $stmt->query);
		$this->assertNull($stmt->options);
	}
	
	public function testCreate2() {
		$stmt = new Statement('findAll', "SELECT * FROM users");
		$this->assertEquals('SELECT * FROM users', $stmt->query);
		$this->assertNull($stmt->options);
	}
	
	public function testCreate3() {
		$stmt = new Statement('findAll', "SELECT * FROM users", Statement::type('obj[user_id:int]'));
		$this->assertEquals('SELECT * FROM users', $stmt->query);
		$this->assertInstanceOf('\eMapper\Statement\Configuration\StatementConfigurationContainer', $stmt->options);
		$this->assertEquals(array('map.type' => 'obj[user_id:int]'), $stmt->options->config);
	}
	
	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testNamespaceCreate0() {
		$ns = new StatementNamespace('*');
	}
	
	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testNamespaceCreate1() {
		$ns = StatementNamespace::create('+');
	}
	
	public function testNamespaceCreate2() {
		$ns = new StatementNamespace('main');
		$this->assertFalse($ns->hasNamespace('ns1'));
		$this->assertFalse($ns->getNamespace('ns1'));
		
		$sub_ns = new StatementNamespace('sub');
		$ns->addNamespace($sub_ns);
		$this->assertTrue($ns->hasNamespace('sub'));
		$this->assertInstanceOf('eMapper\Statement\StatementNamespace', $ns->getNamespace('sub'));
		
		$xtra_ns = new StatementNamespace('xtra');
		$this->assertFalse($xtra_ns->hasStatement('stmt1'));
		$this->assertFalse($xtra_ns->getStatement('stmt1'));
		$stmt = new Statement('stmt1', "SELECT * FROM table");
		$xtra_ns->addStatement($stmt);
		$this->assertTrue($xtra_ns->hasStatement('stmt1'));
		$this->assertInstanceOf('eMapper\Statement\Statement', $xtra_ns->getStatement('stmt1'));
		
		$ns->addNamespace($xtra_ns);
		$this->assertTrue($ns->hasNamespace('xtra'));
		$this->assertInstanceOf('eMapper\Statement\StatementNamespace', $ns->getNamespace('xtra'));
		
		$this->assertTrue($ns->hasStatement('xtra.stmt1'));
		$this->assertInstanceOf('eMapper\Statement\Statement', $ns->getStatement('xtra.stmt1'));
		
		$ref = $ns->buildNamespace('ns2');
		$ref->addStatement(new Statement('find'));		
		$this->assertTrue($ns->hasNamespace('ns2'));
		$this->assertInstanceOf('eMapper\Statement\StatementNamespace', $ns->getNamespace('ns2'));
		$this->assertTrue($ref->hasStatement('find'));
		$this->assertInstanceOf('eMapper\Statement\Statement', $ref->getStatement('find'));
		$this->assertTrue($ns->hasStatement('ns2.find'));
		$this->assertInstanceOf('eMapper\Statement\Statement', $ns->getStatement('ns2.find'));
		
		$ref = $ns->ns('ns3');
		$ref->addStatement(new Statement('find'));
		$this->assertTrue($ns->hasNamespace('ns3'));
		$this->assertInstanceOf('eMapper\Statement\StatementNamespace', $ns->getNamespace('ns3'));
		$this->assertTrue($ref->hasStatement('find'));
		$this->assertInstanceOf('eMapper\Statement\Statement', $ref->getStatement('find'));
		$this->assertTrue($ns->hasStatement('ns3.find'));
		$this->assertInstanceOf('eMapper\Statement\Statement', $ns->getStatement('ns3.find'));
		
		$ref = $ns->ns('ns2');
		$this->assertInstanceOf('eMapper\Statement\StatementNamespace', $ref);
		$ns->ns('ns3')->addStatement(new Statement('findAll'));
		$this->assertTrue($ns->hasStatement('ns3.findAll'));
		$this->assertInstanceOf('eMapper\Statement\Statement', $ns->getStatement('ns3.findAll'));
		
		$ref = $ns->buildStatement('findByPK');
		$this->assertInstanceOf('eMapper\Statement\Statement', $ref);
		$this->assertTrue($ns->hasStatement('findByPK'));
		$this->assertInstanceOf('eMapper\Statement\Statement', $ns->getStatement('findByPK'));
		
		$ref = $ns->stmt('findByName');
		$this->assertInstanceOf('eMapper\Statement\StatementNamespace', $ref);
		$this->assertTrue($ns->hasStatement('findByName'));
		$this->assertInstanceOf('eMapper\Statement\Statement', $ns->getStatement('findByName'));
		
		$this->assertInstanceOf('eMapper\Statement\Statement', $ns->stmt('findByName'));
		
		$ref = $ns->ns('ns4')
		->stmt('findByUserId', "SELECT * FROM users WHERE id = %{i}", Statement::type('obj'))
		->stmt('findByEmail', "SELECT * FROM users WHERE email = %{s}", Statement::type('array'));
		
		$this->assertInstanceOf('eMapper\Statement\StatementNamespace', $ref);
		$this->assertTrue($ns->hasNamespace('ns4'));
		$this->assertTrue($ns->hasStatement('ns4.findByUserId'));
		$this->assertTrue($ns->hasStatement('ns4.findByEmail'));
		
		$stmt = $ns->getStatement('ns4.findByUserId');
		$this->assertEquals(array('map.type' => 'obj'), $stmt->options->config);
		
		$stmt = $ns->getStatement('ns4.findByEmail');
		$this->assertEquals(array('map.type' => 'array'), $stmt->options->config);
	}
}
?>