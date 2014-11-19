<?php
namespace eMapper\Cache\APC;

use eMapper\Cache\AbstractCacheTest;
use SimpleCache\APCProvider;

abstract class AbstractAPCTest extends AbstractCacheTest {
	protected function getProvider() {
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