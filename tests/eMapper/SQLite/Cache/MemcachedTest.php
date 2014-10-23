<?php
namespace eMapper\SQLite\Cache;

use SimpleCache\MemcachedProvider;

/**
 * Tests MemcachedProvider with SQLiteMapper class
 * @author emaphp
 * @group sqlite
 * @group cache
 * @group memcached
 */
class MemcachedTest extends SQLiteCacheTest {
	protected function setUp() {
		try {
			$this->provider = new MemcachedProvider();
			$this->provider->addServer('localhost', 11211);
		}
		catch (\RuntimeException $re) {
			$this->markTestSkipped(
					'The Memcached extension is not available.'
			);
		}

		$this->mapper = $this->getMapper();
		$this->mapper->setCacheProvider($this->provider);
	}
}
?>