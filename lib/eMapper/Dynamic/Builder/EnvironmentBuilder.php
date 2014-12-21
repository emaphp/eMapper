<?php
namespace eMapper\Dynamic\Builder;

use eMapper\Dynamic\Provider\EnvironmentProvider;

/**
 * The EnvironmentBuilder trait provides access to the EnvironmentProvider class
 * in order to obtain dynamically generated execution environment for eMacros expressions
 * @author emaphp
 */
trait EnvironmentBuilder {
	/**
	 * Returns an execution environment
	 * @param array $config
	 * @return \eMapper\Dynamic\Environment\DynamicSQLEnvironment
	 */
	protected function buildEnvironment($config) {
		$environmentId = $config['env.id'];
		
		if (!EnvironmentProvider::hasEnvironment($environmentId))
			EnvironmentProvider::buildEnvironment($environmentId, $config['env.class']);
		
		//setup environment
		$env = EnvironmentProvider::getEnvironment($environmentId);
		$env->setConfig($config);
		return $env;
	}
}