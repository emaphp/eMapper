<?php
namespace eMapper\Dynamic\Builder;

use eMapper\Dynamic\Provider\EnvironmentProvider;
use eMacros\Environment\Environment;

trait EnvironmentBuilder {
	/**
	 * Returns an execution environment
	 * @param array $config
	 * @return Environment
	 */
	protected function buildEnvironment($config) {
		$environmentId = $config['environment.id'];
		
		if (!EnvironmentProvider::hasEnvironment($environmentId)) {
			EnvironmentProvider::buildEnvironment($environmentId, $config['environment.class']);
		}
		
		$env = EnvironmentProvider::getEnvironment($environmentId);
		$env->setConfiguration($config);
		return $env;
	}
}
?>