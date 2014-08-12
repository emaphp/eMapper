<?php
namespace eMapper\PostgreSQL\Cache;

use eMapper\PostgreSQL\PostgreSQLConfig;
use eMapper\Cache\Memcache\AbstractMemcacheTest;

/**
 * 
 * @author emaphp
 * @group cache
 * @group postgre
 * @group memcache
 */
class MemcacheTest extends AbstractMemcacheTest {
	use PostgreSQLConfig;
}
?>