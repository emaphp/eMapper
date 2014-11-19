<?php
namespace eMapper\Cache\Memcached;

use eMapper\Cache\AbstractCacheTest;
use SimpleCache\MemcachedProvider;

abstract class AbstractMemcachedTest extends AbstractCacheTest {
	protected function getProvider() {
		return new MemcachedProvider($this->getPrefix() . 'memcached_test');
	}
	
	public function setUp() {
		try {
			$this->provider = $this->getProvider();
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