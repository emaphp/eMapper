<?php
namespace eMapper\PostgreSQL;

use eMapper\AbstractQueryTest;
use eMapper\Engine\PostgreSQL\PostgreSQLDriver;
use eMapper\SQL\Builder\SelectQueryBuilder;
use eMapper\Query\Attr;

/**
 * PostgreSQL query test
 * @author emaphp
 * @group postgre
 * @group query
 */
class QueryTest extends AbstractQueryTest {
	use PostgreSQLConfig;
	
	public function getDriver() {
		return new PostgreSQLDriver($this->conn_string);
	}
	
	//SELECT regex
	public function testRegex() {
		$query = new SelectQueryBuilder($this->profile);
		$query->setCondition(Attr::code()->matches('^(An?|The) +'));
		list($query, $args) =  $query->build($this->driver, []);
		$this->assertRegExpMatch("/SELECT _t\.product_id,_t\.product_code,_t\.price,_t\.category,_t\.color FROM @@products _t WHERE _t\.product_code ~ #\{([\w]+)\}/", $query, $matches);
		$index = $matches[1];
		$this->assertArrayHasKey($index, $args);
		$this->assertEquals('^(An?|The) +', $args[$index]);
	}
	
	public function testNotRegex() {
		$query = new SelectQueryBuilder($this->profile);
		$query->setCondition(Attr::code()->matches('^(An?|The) +', false));
		list($query, $args) =  $query->build($this->driver, []);
		$this->assertRegExpMatch("/SELECT _t\.product_id,_t\.product_code,_t\.price,_t\.category,_t\.color FROM @@products _t WHERE _t\.product_code !~ #\{([\w]+)\}/", $query, $matches);
		$index = $matches[1];
		$this->assertArrayHasKey($index, $args);
		$this->assertEquals('^(An?|The) +', $args[$index]);
	}
	
	//SELECT iregex
	public function testIRegex() {
		$query = new SelectQueryBuilder($this->profile);
		$query->setCondition(Attr::code()->imatches('^(an?|the) +'));
		list($query, $args) =  $query->build($this->driver, []);
		$this->assertRegExpMatch("/SELECT _t\.product_id,_t\.product_code,_t\.price,_t\.category,_t\.color FROM @@products _t WHERE _t\.product_code ~\* #\{([\w]+)\}/", $query, $matches);
		$index = $matches[1];
		$this->assertArrayHasKey($index, $args);
		$this->assertEquals('^(an?|the) +', $args[$index]);
	}
	
	public function testNotIRegex() {
		$query = new SelectQueryBuilder($this->profile);
		$query->setCondition(Attr::code()->imatches('^(an?|the) +', false));
		list($query, $args) =  $query->build($this->driver, []);
		$this->assertRegExpMatch("/SELECT _t\.product_id,_t\.product_code,_t\.price,_t\.category,_t\.color FROM @@products _t WHERE _t\.product_code !~\* #\{([\w]+)\}/", $query, $matches);
		$index = $matches[1];
		$this->assertArrayHasKey($index, $args);
		$this->assertEquals('^(an?|the) +', $args[$index]);
	}
}
?>