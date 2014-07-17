<?php
namespace eMapper\MySQL;

use eMapper\AbstractQueryTest;
use eMapper\Reflection\Profiler;
use eMapper\MySQL\MySQLTest;
use eMapper\Engine\MySQL\MySQLDriver;
use eMapper\Query\Builder\SelectQueryBuilder;
use eMapper\Query\Attr;

/**
 * MySQL query builder tests
 * @author emaphp
 * @group mysql
 * @group query
 */
class QueryTest extends AbstractQueryTest {
	public function build() {
		$config = MySQLTest::$config;
		$this->driver = new MySQLDriver($config['database'], $config['host'], $config['user'], $config['password']);
		$this->profile = Profiler::getClassProfile('Acme\Entity\Product');
	}
	
	//SELECT regex
	public function testRegex() {
		$query = new SelectQueryBuilder($this->profile);
		$query->setCondition(Attr::code()->regex('^(An?|The) +'));
		list($query, $args) =  $query->build($this->driver, []);
		$this->assertRegExpMatch("/SELECT \* FROM products WHERE product_code REGEXP BINARY #\{([\w]+)\}/", $query, $matches);
		$index = $matches[1];
		$this->assertArrayHasKey($index, $args);
		$this->assertEquals('^(An?|The) +', $args[$index]);
	}
	
	public function testNotRegex() {
		$query = new SelectQueryBuilder($this->profile);
		$query->setCondition(Attr::code()->regex('^(An?|The) +', false));
		list($query, $args) =  $query->build($this->driver, []);
		$this->assertRegExpMatch("/SELECT \* FROM products WHERE product_code NOT REGEXP BINARY #\{([\w]+)\}/", $query, $matches);
		$index = $matches[1];
		$this->assertArrayHasKey($index, $args);
		$this->assertEquals('^(An?|The) +', $args[$index]);
	}
	
	//SELECT iregex
	public function testIRegex() {
		$query = new SelectQueryBuilder($this->profile);
		$query->setCondition(Attr::code()->iregex('^(an?|the) +'));
		list($query, $args) =  $query->build($this->driver, []);
		$this->assertRegExpMatch("/SELECT \* FROM products WHERE product_code REGEXP #\{([\w]+)\}/", $query, $matches);
		$index = $matches[1];
		$this->assertArrayHasKey($index, $args);
		$this->assertEquals('^(an?|the) +', $args[$index]);
	}
	
	public function testNotIRegex() {
		$query = new SelectQueryBuilder($this->profile);
		$query->setCondition(Attr::code()->iregex('^(an?|the) +', false));
		list($query, $args) =  $query->build($this->driver, []);
		$this->assertRegExpMatch("/SELECT \* FROM products WHERE product_code NOT REGEXP #\{([\w]+)\}/", $query, $matches);
		$index = $matches[1];
		$this->assertArrayHasKey($index, $args);
		$this->assertEquals('^(an?|the) +', $args[$index]);
	}
}
?>