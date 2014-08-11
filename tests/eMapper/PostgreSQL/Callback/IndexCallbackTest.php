<?php
namespace eMapper\PostgreSQL\Callback;

use eMapper\PostgreSQL\PostgreSQLConfig;
use eMapper\Callback\AbstractIndexCallbackTest;

/**
 * Test setting an index callback
 *
 * @author emaphp
 * @group callback
 * @group postgre
 */
class IndexCallbackTest extends AbstractIndexCallbackTest {
	use PostgreSQLConfig;
}
?>