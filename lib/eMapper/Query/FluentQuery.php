<?php
namespace eMapper\Query;

use eMapper\Mapper;
use eMapper\Query\Fluent\SelectQuery;

class FluentQuery {
	/**
	 * Data mapper
	 * @var Mapper
	 */
	protected $mapper;
	
	public function __construct(Mapper $mapper) {
		$this->mapper = $mapper;
	}
	
	public function from($table, $alias = null) {
		$select = new SelectQuery($this, $table, $alias);
		return $select;
	}
	
	public function insertInto($table) {
		
	}
	
	public function update($table) {
		
	}
	
	public function deleteFrom($table) {
		
	}
	
	public function getMapper() {
		return $this->mapper;
	}
}
?>