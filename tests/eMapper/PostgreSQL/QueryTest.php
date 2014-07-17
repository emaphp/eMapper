<?php
namespace eMapper\PostgreSQL;

use eMapper\AbstractQueryTest;
use eMapper\Engine\PostgreSQL\PostgreSQLDriver;
use eMapper\Reflection\Profiler;

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
}
?>