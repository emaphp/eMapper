<?php
namespace eMapper\SQLite;

use eMapper\AbstractQueryTest;
use eMapper\Engine\SQLite\SQLiteDriver;
use eMapper\Reflection\Profiler;
use eMapper\Query\Builder\SelectQueryBuilder;
use eMapper\Query\Attr;

/**
 * SQLite query test
 * @author emaphp
 * @group sqlite
 * @group query
 */
class QueryTest extends AbstractQueryTest {
	public function build() {
		$this->driver = new SQLiteDriver(SQLiteTest::$filename);
		$this->profile = Profiler::getClassProfile('Acme\Entity\Product');
	}
	
	//SELECT icontains
	public function testSelectIContains() {
		$query = new SelectQueryBuilder($this->profile);
		$query->setCondition(Attr::code()->icontains('GFX'));
		list($query, $args) = $query->build($this->driver, []);
		$this->assertRegExpMatch("/^SELECT \* FROM @@products WHERE product_code LIKE #\{([\w]+)\}/", $query, $matches);
		$index = $matches[1];
		$this->assertArrayHasKey($index, $args);
		$this->assertEquals('%GFX%', $args[$index]);
	}
	
	public function testSelectNotIContains() {
		$query = new SelectQueryBuilder($this->profile);
		$query->setCondition(Attr::code()->icontains('GFX', false));
		list($query, $args) = $query->build($this->driver, []);
		$this->assertRegExpMatch("/^SELECT \* FROM @@products WHERE product_code NOT LIKE #\{([\w]+)\}/", $query, $matches);
		$index = $matches[1];
		$this->assertArrayHasKey($index, $args);
		$this->assertEquals('%GFX%', $args[$index]);
	}
	
	//SELECT istartswith
	public function testSelectIStartsWith() {
		$query = new SelectQueryBuilder($this->profile);
		$query->setCondition(Attr::code()->istartswith('IND'));
		list($query, $args) = $query->build($this->driver, []);
		$this->assertRegExpMatch("/SELECT \* FROM @@products WHERE product_code LIKE #\{([\w]+)\}/", $query, $matches);
		$index = $matches[1];
		$this->assertArrayHasKey($index, $args);
		$this->assertEquals('IND%', $args[$index]);
	}
	
	public function testSelectNotIStartsWith() {
		$query = new SelectQueryBuilder($this->profile);
		$query->setCondition(Attr::code()->istartswith('IND', false));
		list($query, $args) = $query->build($this->driver, []);
		$this->assertRegExpMatch("/SELECT \* FROM @@products WHERE product_code NOT LIKE #\{([\w]+)\}/", $query, $matches);
		$index = $matches[1];
		$this->assertArrayHasKey($index, $args);
		$this->assertEquals('IND%', $args[$index]);
	}
	
	//SELECT iendswith
	public function testSelectIEndsWith() {
		$query = new SelectQueryBuilder($this->profile);
		$query->setCondition(Attr::code()->iendswith('232'));
		list($query, $args) = $query->build($this->driver, []);
		$this->assertRegExpMatch("/SELECT \* FROM @@products WHERE product_code LIKE #\{([\w]+)\}/", $query, $matches);
		$index = $matches[1];
		$this->assertArrayHasKey($index, $args);
		$this->assertEquals('%232', $args[$index]);
	}
	
	public function testSelectNotIEndsWith() {
		$query = new SelectQueryBuilder($this->profile);
		$query->setCondition(Attr::code()->iendswith('232', false));
		list($query, $args) = $query->build($this->driver, []);
		$this->assertRegExpMatch("/SELECT \* FROM @@products WHERE product_code NOT LIKE #\{([\w]+)\}/", $query, $matches);
		$index = $matches[1];
		$this->assertArrayHasKey($index, $args);
		$this->assertEquals('%232', $args[$index]);
	}
	
	//SELECT regex
	public function testRegex() {
		$query = new SelectQueryBuilder($this->profile);
		$query->setCondition(Attr::code()->matches('^(An?|The) +'));
		list($query, $args) =  $query->build($this->driver, []);
		$this->assertRegExpMatch("/SELECT \* FROM @@products WHERE product_code REGEXP #\{([\w]+)\}/", $query, $matches);
		$index = $matches[1];
		$this->assertArrayHasKey($index, $args);
		$this->assertEquals('^(An?|The) +', $args[$index]);
	}
	
	public function testNotRegex() {
		$query = new SelectQueryBuilder($this->profile);
		$query->setCondition(Attr::code()->matches('^(An?|The) +', false));
		list($query, $args) =  $query->build($this->driver, []);
		$this->assertRegExpMatch("/SELECT \* FROM @@products WHERE product_code NOT REGEXP #\{([\w]+)\}/", $query, $matches);
		$index = $matches[1];
		$this->assertArrayHasKey($index, $args);
		$this->assertEquals('^(An?|The) +', $args[$index]);
	}
	
	//SELECT iregex
	public function testIRegex() {
		$query = new SelectQueryBuilder($this->profile);
		$query->setCondition(Attr::code()->imatches('^(an?|the) +'));
		list($query, $args) =  $query->build($this->driver, []);
		$this->assertRegExpMatch("/SELECT \* FROM @@products WHERE product_code REGEXP #\{([\w]+)\}/", $query, $matches);
		$index = $matches[1];
		$this->assertArrayHasKey($index, $args);
		$this->assertEquals('(?i)^(an?|the) +', $args[$index]);
	}
	
	public function testNotIRegex() {
		$query = new SelectQueryBuilder($this->profile);
		$query->setCondition(Attr::code()->imatches('^(an?|the) +', false));
		list($query, $args) =  $query->build($this->driver, []);
		$this->assertRegExpMatch("/SELECT \* FROM @@products WHERE product_code NOT REGEXP #\{([\w]+)\}/", $query, $matches);
		$index = $matches[1];
		$this->assertArrayHasKey($index, $args);
		$this->assertEquals('(?i)^(an?|the) +', $args[$index]);
	}
}
?>