<?php
namespace eMapper\PostgreSQL\Callback;

use eMapper\PostgreSQL\PostgreSQLConfig;
use eMapper\Callback\AbstractOverrideCallbackTest;

/**
 * Test setting a callback that overrides current query
 *
 * @author emaphp
 * @group callback
 * @group postgre
 */
class OverrideCallbackTest extends AbstractOverrideCallbackTest {
	use PostgreSQLConfig;
}
?>