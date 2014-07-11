<?php
namespace eMapper\Configuration;

trait Configuration {
	/**
	 * Configuration values
	 * @var array
	 */
	public $config = array();
	
	/**
	 * Clones the current object and merges configuration values with the new ones
	 * @param array $values
	 * @param boolean $invert
	 * @throws \InvalidArgumentException
	 * @return Configuration
	*/
	public function merge($values, $invert = false) {
		if (!is_array($values)) {
			throw new \InvalidArgumentException("Configuration values must be defined as an array");
		}
		
		$obj = clone $this;
		$obj->config = ($invert) ? array_merge($values, $this->config) : array_merge($this->config, $values);
		return $obj;
	}
	
	/**
	 * Creates a copy of this object removing the given configuration options
	 * Ex: $config->discard('map.type, 'map.params');
	 * @return Configuration
	 */
	public function discard() {
		$filter = func_get_args();
		$keys = array_keys($this->config);
		$obj = clone $this;
		
		for ($i = 0, $n = count($keys); $i < $n; $i++) {
			$key = $keys[$i];
			
			if (in_array($key, $filter)) {
				unset($obj->config[$key]);
			}
		}
		
		return $obj;
	}
	
	/**
	 * Declares a transient configuration value
	 * @param string $name
	 * @param mixed $value
	 * @throws \InvalidArgumentException
	 * @return Configuration
	 */
	public function option($name, $value) {
		if (!is_string($name) || empty($name)) {
			throw new \InvalidArgumentException("Option name must be a valid string");
		}
	
		return $this->merge([$name => $value]);
	}
	
	/**
	 * Declares a non-transient configuration value
	 * @param string $name
	 * @param mixed $value
	 * @throws \InvalidArgumentException
	 */
	public function set($name, $value) {
		if (!is_string($name) || empty($name)) {
			throw new \InvalidArgumentException("Option name must be a valid string");
		}
		
		$this->config[$name] = $value;
	}
	
	/**
	 * Obtains a configuration value
	 * @param string $name
	 * @throws \InvalidArgumentException
	 * @return mixed
	 */
	public function get($name) {
		if (!is_string($name) || empty($name)) {
			throw new \InvalidArgumentException("Option name must be a valid string");
		}
		
		if (array_key_exists($name, $this->config)) {
			return $this->config[$name];
		}
		
		return null;
	}
	
	/**
	 * Pushes a configuration value
	 * @param string $key
	 * @param mixed $value
	 * @return Configuration
	 */
	public function push($key, $value) {
		$obj = clone $this;
		
		if (array_key_exists($key, $obj->config)) {
			array_push($obj->config, $value);
		}
		else {
			$obj->config[$key] = [$value];
		}
		
		return $obj;
	}
}
?>