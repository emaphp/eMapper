<?php
namespace eMapper\SQLite;

use eMapper\AbstractManagerTest;
use eMapper\Query\Attr;

/**
 * SQLite manager test
 * @author emaphp
 * @group sqlite
 * @group manager
 */
class ManagerTest extends AbstractManagerTest {
	use SQLiteConfig;
	
	public function testSQLiContains(){ 
		$query = "SELECT _t.product_id,_t.product_code,_t.price,_t.category,_t.color FROM products _t WHERE _t.product_code LIKE '%GFX%'";
		$this->productsManager
		->debug(function ($q) use ($query) {
			$this->assertEquals($query, $q);
		})
		->find(Attr::code()->icontains('GFX'));
	}
	
	public function testSQLNotiContains(){
		$query = "SELECT _t.product_id,_t.product_code,_t.price,_t.category,_t.color FROM products _t WHERE _t.product_code NOT LIKE '%GFX%'";
		$this->productsManager
		->debug(function ($q) use ($query) {
			$this->assertEquals($query, $q);
		})
		->find(Attr::code()->icontains('GFX', false));
	}
	
	public function testSQLiStartsWith(){
		$query = "SELECT _t.product_id,_t.product_code,_t.price,_t.category,_t.color FROM products _t WHERE _t.product_code LIKE 'IND%'";
		$this->productsManager
		->debug(function ($q) use ($query) {
			$this->assertEquals($query, $q);
		})
		->find(Attr::code()->istartswith('IND'));
	}
	
	public function testSQLNotiStartsWith(){
		$query = "SELECT _t.product_id,_t.product_code,_t.price,_t.category,_t.color FROM products _t WHERE _t.product_code NOT LIKE 'IND%'";
		$this->productsManager
		->debug(function ($q) use ($query) {
			$this->assertEquals($query, $q);
		})
		->find(Attr::code()->istartswith('IND', false));
	}
	
	public function testSQLiEndsWith() {
		$query = "SELECT _t.product_id,_t.product_code,_t.price,_t.category,_t.color FROM products _t WHERE _t.product_code LIKE '%232'";
		$this->productsManager
		->debug(function ($q) use ($query) {
			$this->assertEquals($query, $q);
		})
		->find(Attr::code()->iendswith('232'));
	}
	
	public function testSQLNotiEndsWith() {
		$query = "SELECT _t.product_id,_t.product_code,_t.price,_t.category,_t.color FROM products _t WHERE _t.product_code NOT LIKE '%232'";
		$this->productsManager
		->debug(function ($q) use ($query) {
			$this->assertEquals($query, $q);
		})
		->find(Attr::code()->iendswith('232', false));
	}
	
	public function testSQLRegex() {
		$query = "SELECT _t.product_id,_t.product_code,_t.price,_t.category,_t.color FROM products _t WHERE _t.product_code REGEXP '^(An?|The) +'";
		$this->productsManager
		->debug(function ($q) use ($query) {
			$this->assertEquals($query, $q);
		})
		->find(Attr::code()->matches('^(An?|The) +'));
	}
	
	public function testSQLNotRegex() {
		$query = "SELECT _t.product_id,_t.product_code,_t.price,_t.category,_t.color FROM products _t WHERE _t.product_code NOT REGEXP '^(An?|The) +'";
		$this->productsManager
		->debug(function ($q) use ($query) {
			$this->assertEquals($query, $q);
		})
		->find(Attr::code()->matches('^(An?|The) +', false));
	}
	
	public function testSQLiRegex() {
		$query = "SELECT _t.product_id,_t.product_code,_t.price,_t.category,_t.color FROM products _t WHERE _t.product_code REGEXP '(?i)^(an?|the) +'";
		$this->productsManager
		->debug(function ($q) use ($query) {
			$this->assertEquals($query, $q);
		})
		->find(Attr::code()->imatches('^(an?|the) +'));
	}
	
	public function testSQLNotiRegex() {
		$query = "SELECT _t.product_id,_t.product_code,_t.price,_t.category,_t.color FROM products _t WHERE _t.product_code NOT REGEXP '(?i)^(an?|the) +'";
		$this->productsManager
		->debug(function ($q) use ($query) {
			$this->assertEquals($query, $q);
		})
		->find(Attr::code()->imatches('^(an?|the) +', false));
	}
}