<?php
namespace eMapper\Dynamic\Environment;

trait ConfigurableEnvironment {
	/**
	 * Environment configuration
	 * @var array
	 */
	public $config;
	
	public function setConfiguration($config) {
		$this->config = $config;
	}
}
?>