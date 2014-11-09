<?php
namespace eMapper\Query\Fluent;

use eMapper\Query\FluentQuery;

abstract class AbstractQuery {
	protected $query;
	protected $joins = [];
	
	public function __construct(FluentQuery $query) {
		$this->query = $query;
	}
	
	public abstract function build();
	
	public function __toString() {
		return $this->build();
	}
	
	public function run() {
		$query = $this->build();
	}	
}
?>