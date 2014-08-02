<?php
namespace eMapper;

use eMapper\SQL\Statement;
use Acme\Result\UserResultMap;
use Acme\Parameter\ProductParameterMap;
use eMapper\SQL\SQLNamespace;

/**
 * Tests creating Statement and StatementNamespace instances and applying different configuration
 * 
 * @author emaphp
 * @group statement
 */
class StatementTest extends \PHPUnit_Framework_TestCase {
	public function testConfigType() {
		$config = Statement::config(['map.type' => 'i']);
		$this->assertInstanceOf('\eMapper\SQL\Configuration\StatementConfigurationContainer', $config);
		$this->assertTrue($config->hasOption('map.type'));
		$this->assertEquals('i', $config->getOption('map.type'));
		
		$config = Statement::config()->type('i');
		$this->assertInstanceOf('\eMapper\SQL\Configuration\StatementConfigurationContainer', $config);
		$this->assertTrue($config->hasOption('map.type'));
		$this->assertEquals('i', $config->getOption('map.type'));
		
		$config = Statement::type('i');
		$this->assertInstanceOf('\eMapper\SQL\Configuration\StatementConfigurationContainer', $config);
		$this->assertTrue($config->hasOption('map.type'));
		$this->assertEquals('i', $config->getOption('map.type'));
	}
	
	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testConfigResultMap0() {
		$config = Statement::config()->result_map(null);
	}
	
	public function testConfigResultMap1() {
		$config = Statement::config()->result_map('Acme\Result\UserResultMap');
		$this->assertInstanceOf('\eMapper\SQL\Configuration\StatementConfigurationContainer', $config);
		$this->assertTrue($config->hasOption('map.result'));
		$this->assertEquals('Acme\Result\UserResultMap', $config->getOption('map.result'));
		
		$config = Statement::config()->result_map(new UserResultMap());
		$this->assertInstanceOf('\eMapper\SQL\Configuration\StatementConfigurationContainer', $config);
		$this->assertTrue($config->hasOption('map.result'));
		$this->assertEquals('Acme\Result\UserResultMap', $config->getOption('map.result'));
	}
	
	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testConfigParameterMap0() {
		$config = Statement::config()->parameter_map(null);
	}
	
	public function testConfigParameterMap1() {
		$config = Statement::config()->parameter_map('Acme\Parameter\ProductParameterMap');
		$this->assertInstanceOf('\eMapper\SQL\Configuration\StatementConfigurationContainer', $config);
		$this->assertTrue($config->hasOption('map.parameter'));
		$this->assertEquals('Acme\Parameter\ProductParameterMap', $config->getOption('map.parameter'));
		
		$config = Statement::config()->parameter_map(new ProductParameterMap());
		$this->assertInstanceOf('\eMapper\SQL\Configuration\StatementConfigurationContainer', $config);
		$this->assertTrue($config->hasOption('map.parameter'));
		$this->assertEquals('Acme\Parameter\ProductParameterMap', $config->getOption('map.parameter'));
	}
	
	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testConfigEach0() {
		$config = Statement::config()->each(0);
	}
	
	public function testConfigEach1() {
		$config = Statement::config()->each(function() {});
		$this->assertTrue($config->hasOption('callback.each'));
		$this->assertInstanceOf('Closure', $config->getOption('callback.each'));
	}
	
	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testConfigFilter0() {
		$config = Statement::config()->filter(0);
	}
	
	public function testConfigFilter1() {
		$config = Statement::config()->filter(function() {});
		$this->assertTrue($config->hasOption('callback.filter'));
		$this->assertInstanceOf('Closure', $config->getOption('callback.filter'));
	}
	
	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testConfigNoRows0() {
		$config = Statement::config()->no_rows(0);
	}
	
	public function testConfigNoRows1() {
		$config = Statement::config()->no_rows(function() {});
		$this->assertTrue($config->hasOption('callback.no_rows'));
		$this->assertInstanceOf('Closure', $config->getOption('callback.no_rows'));
	}
	
	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testConfigQueryOverride0() {
		$config = Statement::config()->query_override(0);
	}
	
	public function testConfigQueryOverride1() {
		$config = Statement::config()->query_override(function() {});
		$this->assertTrue($config->hasOption('callback.query'));
		$this->assertInstanceOf('Closure', $config->getOption('callback.query'));
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
		$this->assertTrue($config->hasOption('cache.key'));
		$this->assertTrue($config->hasOption('cache.ttl'));
		$this->assertEquals('USERS', $config->getOption('cache.key'));
		$this->assertEquals(0, $config->getOption('cache.ttl'));
		
		$config = Statement::config()->cache('USERS', 120);
		$this->assertTrue($config->hasOption('cache.key'));
		$this->assertTrue($config->hasOption('cache.ttl'));
		$this->assertEquals('USERS', $config->getOption('cache.key'));
		$this->assertEquals(120, $config->getOption('cache.ttl'));
	}
	
	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testCreate0() {
		$stmt = new Statement('*');
	}
	
	public function testCreate1() {
		$stmt = new Statement('findAll');
		$this->assertEquals('', $stmt->getQuery());
		$this->assertNull($stmt->getOptions());
	}
	
	public function testCreate2() {
		$stmt = new Statement('findAll', "SELECT * FROM users");
		$this->assertEquals('SELECT * FROM users', $stmt->getQuery());
		$this->assertNull($stmt->getOptions());
	}
	
	public function testCreate3() {
		$stmt = new Statement('findAll', "SELECT * FROM users", Statement::type('obj[user_id:int]'));
		$this->assertEquals('SELECT * FROM users', $stmt->getQuery());
		$this->assertInstanceOf('\eMapper\SQL\Configuration\StatementConfigurationContainer', $stmt->getOptions());
		$this->assertTrue($stmt->getOptions()->hasOption('map.type'));
		$this->assertEquals('obj[user_id:int]', $stmt->getOptions()->getOption('map.type'));
	}
	
	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testNamespaceCreate0() {
		$ns = new SQLNamespace('*');
	}
	
	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testNamespaceCreate1() {
		$ns = SQLNamespace::create('+');
	}
	
	public function testNamespaceCreate2() {
		$ns = new SQLNamespace('main');
		$this->assertFalse($ns->hasNamespace('ns1'));
		$this->assertFalse($ns->getNamespace('ns1'));
		
		$sub_ns = new SQLNamespace('sub');
		$ns->addNamespace($sub_ns);
		$this->assertTrue($ns->hasNamespace('sub'));
		$this->assertInstanceOf('eMapper\SQL\SQLNamespace', $ns->getNamespace('sub'));
		
		$xtra_ns = new SQLNamespace('xtra');
		$this->assertFalse($xtra_ns->hasStatement('stmt1'));
		$this->assertFalse($xtra_ns->getStatement('stmt1'));
		$stmt = new Statement('stmt1', "SELECT * FROM table");
		$xtra_ns->addStatement($stmt);
		$this->assertTrue($xtra_ns->hasStatement('stmt1'));
		$this->assertInstanceOf('eMapper\SQL\Statement', $xtra_ns->getStatement('stmt1'));
		
		$ns->addNamespace($xtra_ns);
		$this->assertTrue($ns->hasNamespace('xtra'));
		$this->assertInstanceOf('eMapper\SQL\SQLNamespace', $ns->getNamespace('xtra'));
		
		$this->assertTrue($ns->hasStatement('xtra.stmt1'));
		$this->assertInstanceOf('eMapper\SQL\Statement', $ns->getStatement('xtra.stmt1'));
		
		$ref = $ns->buildNamespace('ns2');
		$ref->addStatement(new Statement('find'));		
		$this->assertTrue($ns->hasNamespace('ns2'));
		$this->assertInstanceOf('eMapper\SQL\SQLNamespace', $ns->getNamespace('ns2'));
		$this->assertTrue($ref->hasStatement('find'));
		$this->assertInstanceOf('eMapper\SQL\Statement', $ref->getStatement('find'));
		$this->assertTrue($ns->hasStatement('ns2.find'));
		$this->assertInstanceOf('eMapper\SQL\Statement', $ns->getStatement('ns2.find'));
		
		$ref = $ns->ns('ns3');
		$ref->addStatement(new Statement('find'));
		$this->assertTrue($ns->hasNamespace('ns3'));
		$this->assertInstanceOf('eMapper\SQL\SQLNamespace', $ns->getNamespace('ns3'));
		$this->assertTrue($ref->hasStatement('find'));
		$this->assertInstanceOf('eMapper\SQL\Statement', $ref->getStatement('find'));
		$this->assertTrue($ns->hasStatement('ns3.find'));
		$this->assertInstanceOf('eMapper\SQL\Statement', $ns->getStatement('ns3.find'));
		
		$ref = $ns->ns('ns2');
		$this->assertInstanceOf('eMapper\SQL\SQLNamespace', $ref);
		$ns->ns('ns3')->addStatement(new Statement('findAll'));
		$this->assertTrue($ns->hasStatement('ns3.findAll'));
		$this->assertInstanceOf('eMapper\SQL\Statement', $ns->getStatement('ns3.findAll'));
		
		$ref = $ns->buildStatement('findByPK');
		$this->assertInstanceOf('eMapper\SQL\Statement', $ref);
		$this->assertTrue($ns->hasStatement('findByPK'));
		$this->assertInstanceOf('eMapper\SQL\Statement', $ns->getStatement('findByPK'));
		
		$ref = $ns->stmt('findByName');
		$this->assertInstanceOf('eMapper\SQL\SQLNamespace', $ref);
		$this->assertTrue($ns->hasStatement('findByName'));
		$this->assertInstanceOf('eMapper\SQL\Statement', $ns->getStatement('findByName'));
		
		$this->assertInstanceOf('eMapper\SQL\Statement', $ns->stmt('findByName'));
		
		$ref = $ns->ns('ns4')
		->stmt('findByUserId', "SELECT * FROM users WHERE id = %{i}", Statement::type('obj'))
		->stmt('findByEmail', "SELECT * FROM users WHERE email = %{s}", Statement::type('array'));
		
		$this->assertInstanceOf('eMapper\SQL\SQLNamespace', $ref);
		$this->assertTrue($ns->hasNamespace('ns4'));
		$this->assertTrue($ns->hasStatement('ns4.findByUserId'));
		$this->assertTrue($ns->hasStatement('ns4.findByEmail'));
		
		$stmt = $ns->getStatement('ns4.findByUserId');
		$this->assertTrue($stmt->getOptions()->hasOption('map.type'));
		$this->assertEquals('obj', $stmt->getOptions()->getOption('map.type'));
		
		$stmt = $ns->getStatement('ns4.findByEmail');
		$this->assertTrue($stmt->getOptions()->hasOption('map.type'));
		$this->assertEquals('array', $stmt->getOptions()->getOption('map.type'));
	}
}
?>