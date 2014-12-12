<?php
namespace eMapper\Statement;

/**
 * The Statement class wraps a SQL query along with the corresponding configuration values.
 * @author emaphp
 */
class Statement {
	/**
	 * Statement query
	 * @var string
	 */
	protected $query;
	
	/**
	 * Statement options
	 * @var array
	 */
	protected $options;
	
	/**
	 * Creates a new Statement instance
	 * @param string $query
	 * @param array $options
	 * @throws \InvalidArgumentException
	 */
	public function __construct($query, array $options) {
		if (!is_string($query))
			throw new \InvalidArgumentException("Query is not a valid string");

		$this->query = $query;
		$this->options = $options;
	}

	public function getQuery() {
		return $this->query;
	}
	
	public function getOptions() {
		return $this->options;
	}
}