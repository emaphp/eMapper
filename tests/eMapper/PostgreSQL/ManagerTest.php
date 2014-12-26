<?php
namespace eMapper\PostgreSQL;

use eMapper\AbstractManagerTest;
use eMapper\Engine\PostgreSQL\PostgreSQLDriver;
use Acme\Type\RGBColorTypeHandler;
use eMapper\Query\Attr;
use eMapper\Mapper;

/**
 * PostgreSQL manager test
 * @author emaphp
 * @group postgre
 * @group manager
 */
class ManagerTest extends AbstractManagerTest {
	use PostgreSQLConfig;
	
	public function testSQLRegex() {
		$query = "SELECT _t.product_id,_t.product_code,_t.price,_t.category,_t.color FROM products _t WHERE _t.product_code ~ '^(An?|The) +'";
		$this->productsManager
		->debug(function ($q) use ($query) {
			$this->assertEquals($query, $q);
		})
		->find(Attr::code()->matches('^(An?|The) +'));
	}
	
	public function testSQLNotRegex() {
		$query = "SELECT _t.product_id,_t.product_code,_t.price,_t.category,_t.color FROM products _t WHERE _t.product_code !~ '^(An?|The) +'";
		$this->productsManager
		->debug(function ($q) use ($query) {
			$this->assertEquals($query, $q);
		})
		->find(Attr::code()->matches('^(An?|The) +', false));
	}
	
	public function testSQLiRegex() {
		$query = "SELECT _t.product_id,_t.product_code,_t.price,_t.category,_t.color FROM products _t WHERE _t.product_code ~* '^(an?|the) +'";
		$this->productsManager
		->debug(function ($q) use ($query) {
			$this->assertEquals($query, $q);
		})
		->find(Attr::code()->imatches('^(an?|the) +'));
	}
	
	public function testSQLNotiRegex() {
		$query = "SELECT _t.product_id,_t.product_code,_t.price,_t.category,_t.color FROM products _t WHERE _t.product_code !~* '^(an?|the) +'";
		$this->productsManager
		->debug(function ($q) use ($query) {
			$this->assertEquals($query, $q);
		})
		->find(Attr::code()->imatches('^(an?|the) +', false));
	}
}
?>