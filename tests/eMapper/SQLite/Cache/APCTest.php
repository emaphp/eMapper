<?php
namespace eMapper\SQLite\Cache;

use SimpleCache\APCProvider;

/**
 * Tests APCProvider with SQLiteMapper class
 * @author emaphp
 * @group sqlite
 * @group cache
 * @group apc
 */
class APCTest extends SQLiteCacheTest {
	public function getProvider() {
		return new APCProvider();
	}
	
	public function setUp() {
		try {
			$this->provider = $this->getProvider();
		}
		catch (\RuntimeException $re) {
			$this->markTestSkipped(
					'The APC extension is not available.'
			);
		}
		
		$this->mapper = $this->getMapper();
		$this->mapper->setCacheProvider($this->provider);
	}
}
?>