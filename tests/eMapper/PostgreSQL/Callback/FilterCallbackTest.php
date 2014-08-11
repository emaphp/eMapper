<?php
namespace eMapper\PostgreSQL\Callback;

use eMapper\PostgreSQL\PostgreSQLConfig;
use eMapper\Callback\AbstractFilterCallbackTest;

/**
 * Test setting a filter callback
 *
 * @author emaphp
 * @group callback
 * @group postgre
 */
class FilterCallbackTest extends AbstractFilterCallbackTest {
	use PostgreSQLConfig;
}
?>