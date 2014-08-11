<?php
namespace eMapper\PostgreSQL\Callback;

use eMapper\PostgreSQL\PostgreSQLConfig;
use eMapper\Callback\AbstractEachCallbackTest;

/**
 * Tests applying a user-defined callback through the each method
 *
 * @author emaphp
 * @group callback
 * @group postgre
 */
class EachCallbackTest extends AbstractEachCallbackTest {
	use PostgreSQLConfig;
}
?>