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
	public function getProvider() {
		return new MemcacheProvider();
	}
	
	public function setUp() {
		try {
			$this->provider = $this->getProvider();
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