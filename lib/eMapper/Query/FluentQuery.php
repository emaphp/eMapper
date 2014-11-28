<?php
namespace eMapper\Query;

use eMapper\Mapper;
use eMapper\SQL\Fluent\FluentSelect;
use eMapper\SQL\Fluent\FluentDelete;
use eMapper\SQL\Fluent\FluentUpdate;
use eMapper\SQL\Fluent\FluentInsert;

/**
 * The FluentQuery class provides a series of method for fluent queries creation
 * @author emaphp
 */
class FluentQuery {
	/**
	 * Data mapper
	 * @var Mapper
	 */
	protected $mapper;
	
	public function __construct(Mapper $mapper) {
		$this->mapper = $mapper;
	}
	
	/**
	 * Returns a new FluentSelect instance
	 * @param string $table
	 * @param string $alias
	 * @return \eMapper\SQL\Fluent\FluentSelect
	 */
	public function from($table, $alias = null) {
		return new FluentSelect($this, $table, $alias);
	}
	
	/**
	 * Returns a new FluentInsert instance
	 * @param string $table
	 * @return \eMapper\SQL\Fluent\FluentInsert
	 */
	public function insertInto($table) {
		return new FluentInsert($this, $table);
	}
	
	/**
	 * Returns a new FluentUpdate instance
	 * @param string $table
	 * @param string $alias
	 * @return \eMapper\SQL\Fluent\FluentUpdate
	 */
	public function update($table, $alias = null) {
		return new FluentUpdate($this, $table, $alias);
	}
	
	/**
	 * Returns a new FluentDelete instance
	 * @param string $table
	 * @param string $alias
	 * @return \eMapper\SQL\Fluent\FluentDelete
	 */
	public function deleteFrom($table, $alias = null) {
		return new FluentDelete($this, $table, $alias);
	}
	
	public function getMapper() {
		return $this->mapper;
	}
}
?>