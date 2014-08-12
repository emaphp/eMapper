<?php
namespace eMapper\Cache\APC;

use eMapper\Cache\AbstractCacheTest;
use eMapper\Cache\APCProvider;

abstract class AbstractAPCTest extends AbstractCacheTest {
	protected function setUp() {
		try {
			$this->provider = new APCProvider();
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