<?php
namespace eMapper\Query;

use eMapper\Mapper;
use eMapper\SQL\Fluent\SelectQuery;
use eMapper\SQL\Fluent\DeleteQuery;
use eMapper\SQL\Fluent\UpdateQuery;
use eMapper\SQL\Fluent\InsertQuery;

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
		return new SelectQuery($this, $table, $alias);
	}
	
	public function insertInto($table) {
		return new InsertQuery($this, $table);
	}
	
	public function update($table, $alias = null) {
		return new UpdateQuery($this, $table, $alias);
	}
	
	public function deleteFrom($table, $alias = null) {
		return new DeleteQuery($this, $table, $alias);
	}
	
	public function getMapper() {
		return $this->mapper;
	}
}
?>