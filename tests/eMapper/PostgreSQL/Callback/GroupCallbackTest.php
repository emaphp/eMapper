<?php
namespace eMapper\PostgreSQL\Callback;

use eMapper\PostgreSQL\PostgreSQLConfig;
use eMapper\Callback\AbstractGroupCallbackTest;

/**
 * Test setting a grouping callback
 *
 * @author emaphp
 * @group callback
 * @group postgre
 */
class GroupCallbackTest extends AbstractGroupCallbackTest {
	use PostgreSQLConfig;
}
?>