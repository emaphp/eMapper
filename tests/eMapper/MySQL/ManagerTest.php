<?php
namespace eMapper\MySQL;

use eMapper\AbstractManagerTest;
use eMapper\Mapper;
use eMapper\Engine\MySQL\MySQLDriver;
use Acme\Type\RGBColorTypeHandler;
use eMapper\Query\Attr;

/**
 * MySQL manager test
 * @author emaphp
 * @group mysql
 * @group manager
 */
class ManagerTest extends AbstractManagerTest {
	use MySQLConfig;
	
	public function testSQLRegex() {
		$query = "SELECT _t.product_id,_t.product_code,_t.price,_t.category,_t.color FROM products _t WHERE _t.product_code REGEXP BINARY '^(An?|The) +'";
		$this->productsManager
		->debug(function ($q) use ($query) {
			$this->assertEquals($query, $q);
		})
		->find(Attr::code()->matches('^(An?|The) +'));
	}
	
	public function testSQLNotRegex() {
		$query = "SELECT _t.product_id,_t.product_code,_t.price,_t.category,_t.color FROM products _t WHERE _t.product_code NOT REGEXP BINARY '^(An?|The) +'";
		$this->productsManager
		->debug(function ($q) use ($query) {
			$this->assertEquals($query, $q);
		})
		->find(Attr::code()->matches('^(An?|The) +', false));
	}
	
	public function testSQLiRegex() {
		$query = "SELECT _t.product_id,_t.product_code,_t.price,_t.category,_t.color FROM products _t WHERE _t.product_code REGEXP '^(an?|the) +'";
		$this->productsManager
		->debug(function ($q) use ($query) {
			$this->assertEquals($query, $q);
		})
		->find(Attr::code()->imatches('^(an?|the) +'));
	}
	
	public function testSQLNotiRegex() {
		$query = "SELECT _t.product_id,_t.product_code,_t.price,_t.category,_t.color FROM products _t WHERE _t.product_code NOT REGEXP '^(an?|the) +'";
		$this->productsManager
		->debug(function ($q) use ($query) {
			$this->assertEquals($query, $q);
		})
		->find(Attr::code()->imatches('^(an?|the) +', false));
	}
}