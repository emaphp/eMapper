<?php
namespace eMapper\SQLite;

use eMapper\AbstractQueryTest;
use eMapper\Engine\SQLite\SQLiteDriver;
use eMapper\Reflection\Profiler;

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
}
?>