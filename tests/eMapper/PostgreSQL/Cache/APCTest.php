<?php
namespace eMapper\PostgreSQL\Cache;

use eMapper\PostgreSQL\PostgreSQLConfig;
use eMapper\Cache\APC\AbstractAPCTest;

/**
 * Test APCProvider with a PostgreSQL connection
 * @author emaphp
 * @group postgre
 * @group cache
 * @group apc
 */
class APCTest extends AbstractAPCTest {
	use PostgreSQLConfig;
}
?>