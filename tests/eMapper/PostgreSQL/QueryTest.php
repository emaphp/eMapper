<?php
namespace eMapper\PostgreSQL;

use eMapper\AbstractQueryTest;
use eMapper\Engine\PostgreSQL\PostgreSQLDriver;
use eMapper\Reflection\Profiler;
use eMapper\Query\Builder\SelectQueryBuilder;
use eMapper\Query\Attr;

/**
 * PostgreSQL query test
 * @author emaphp
 * @group postgre
 * @group query
 */
class QueryTest extends AbstractQueryTest {
	public function build() {
		$connection_string = PostgreSQLTest::$connstring;
		$this->driver = new PostgreSQLDriver($connection_string);
		$this->profile = Profiler::getClassProfile('Acme\Entity\Product');
	}
	
	//SELECT regex
	public function testRegex() {
		$query = new SelectQueryBuilder($this->profile);
		$query->setCondition(Attr::code()->matches('^(An?|The) +'));
		list($query, $args) =  $query->build($this->driver, []);
		$this->assertRegExpMatch("/SELECT \* FROM @@products WHERE product_code ~ #\{([\w]+)\}/", $query, $matches);
		$index = $matches[1];
		$this->assertArrayHasKey($index, $args);
		$this->assertEquals('^(An?|The) +', $args[$index]);
	}
	
	public function testNotRegex() {
		$query = new SelectQueryBuilder($this->profile);
		$query->setCondition(Attr::code()->matches('^(An?|The) +', false));
		list($query, $args) =  $query->build($this->driver, []);
		$this->assertRegExpMatch("/SELECT \* FROM @@products WHERE product_code !~ #\{([\w]+)\}/", $query, $matches);
		$index = $matches[1];
		$this->assertArrayHasKey($index, $args);
		$this->assertEquals('^(An?|The) +', $args[$index]);
	}
	
	//SELECT iregex
	public function testIRegex() {
		$query = new SelectQueryBuilder($this->profile);
		$query->setCondition(Attr::code()->imatches('^(an?|the) +'));
		list($query, $args) =  $query->build($this->driver, []);
		$this->assertRegExpMatch("/SELECT \* FROM @@products WHERE product_code ~\* #\{([\w]+)\}/", $query, $matches);
		$index = $matches[1];
		$this->assertArrayHasKey($index, $args);
		$this->assertEquals('^(an?|the) +', $args[$index]);
	}
	
	public function testNotIRegex() {
		$query = new SelectQueryBuilder($this->profile);
		$query->setCondition(Attr::code()->imatches('^(an?|the) +', false));
		list($query, $args) =  $query->build($this->driver, []);
		$this->assertRegExpMatch("/SELECT \* FROM @@products WHERE product_code !~\* #\{([\w]+)\}/", $query, $matches);
		$index = $matches[1];
		$this->assertArrayHasKey($index, $args);
		$this->assertEquals('^(an?|the) +', $args[$index]);
	}
}
?>