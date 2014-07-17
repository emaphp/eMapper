<?php
namespace eMapper\MySQL;

use eMapper\AbstractQueryTest;
use eMapper\Reflection\Profiler;
use eMapper\MySQL\MySQLTest;
use eMapper\Engine\MySQL\MySQLDriver;

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
}
?>