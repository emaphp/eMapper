<?php
namespace eMapper\MySQL\Cache;

use eMapper\MySQL\MySQLConfig;
use eMapper\Cache\Memcached\AbstractMemcachedTest;

/**
 * Test MemcachedProvider with a MySQL connection
 * @author emaphp
 * @group mysql
 * @group cache
 * @group memcached
 */
class MemcachedTest extends AbstractMemcachedTest {
	use MySQLConfig;
}

?>