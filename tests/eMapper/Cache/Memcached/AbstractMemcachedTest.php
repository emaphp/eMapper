<?php
namespace eMapper\Cache\Memcached;

use eMapper\Cache\AbstractCacheTest;
use eMapper\Cache\MemcachedProvider;

abstract class AbstractMemcachedTest extends AbstractCacheTest {
	protected function setUp() {
		try {
			$this->provider = new MemcachedProvider($this->getPrefix() . 'memcached_test');
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