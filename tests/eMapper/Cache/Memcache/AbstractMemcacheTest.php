<?php
namespace eMapper\Cache\Memcache;

use eMapper\Cache\AbstractCacheTest;
use SimpleCache\MemcacheProvider;

abstract class AbstractMemcacheTest extends AbstractCacheTest {
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