<?php
namespace eMapper\PostgreSQL\Cache;

use eMapper\PostgreSQL\PostgreSQLConfig;
use eMapper\Cache\Memcached\AbstractMemcachedTest;

/**
 * Test MemcachedProvider with MySQLMapper class
 * @author emaphp
 * @group postgre
 * @group cache
 * @group memcached
 */
class MemcachedTest extends AbstractMemcachedTest {
	use PostgreSQLConfig;
}
?>