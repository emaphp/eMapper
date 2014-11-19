<?php
namespace eMapper\Query;

use eMapper\Mapper;
use eMapper\SQL\Fluent\SelectQuery;

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
	
	public function update($table, $alias = null) {
		
	}
	
	public function deleteFrom($table, $alias = null) {
		
	}
	
	public function getMapper() {
		return $this->mapper;
	}
}
?>