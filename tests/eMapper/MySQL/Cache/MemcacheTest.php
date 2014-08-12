<?php
namespace eMapper\MySQL\Cache;

use eMapper\MySQL\MySQLConfig;
use eMapper\Cache\Memcache\AbstractMemcacheTest;

/**
 * Test MemcacheProvider with a MySQL connection
 * @author emaphp
 * @group mysql
 * @group cache
 * @group memcache
 */
class MemcacheTest extends AbstractMemcacheTest {
	use MySQLConfig;
}
?>