<?php
namespace eMapper\SQLite\Cache;

use SimpleCache\MemcacheProvider;

/**
 * Tests MemcacheProvider with SQLiteMapper class
 * @group sqlite
 * @group cache
 * @group memcache
 */
class MemcacheTest extends SQLiteCacheTest {	
	protected function setUp() {
		try {
			$this->provider = new MemcacheProvider();
		}
		catch (\RuntimeException $re) {
			$this->markTestSkipped(
					'The Memcache extension is not available.'
			);
		}
		
		$this->mapper = $this->getMapper();
		$this->mapper->setCacheProvider($this->provider);
	}
}
?>