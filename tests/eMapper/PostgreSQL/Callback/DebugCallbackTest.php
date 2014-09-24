<?php
namespace eMapper\PostgreSQL\Callback;

use eMapper\PostgreSQL\PostgreSQLConfig;
use eMapper\Callback\AbstractDebugCallbackTest;

/**
 * Test setting a debug callback for the current query
 *
 * @author emaphp
 * @group callback
 * @group postgre
 */
class DebugCallbackTest extends AbstractDebugCallbackTest {
	use PostgreSQLConfig;
}
?>