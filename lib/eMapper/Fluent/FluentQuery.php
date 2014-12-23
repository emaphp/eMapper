<?php
namespace eMapper\Fluent;

use eMapper\Fluent\Query\FluentSelect;
use eMapper\Fluent\Query\FluentDelete;
use eMapper\Fluent\Query\FluentUpdate;
use eMapper\Fluent\Query\FluentInsert;
use eMapper\Reflection\ClassProfile;
use eMapper\Mapper;

/**
 * The FluentQuery class provides a series of method for generating fluent queries.
 * @author emaphp
 */
class FluentQuery {
	/**
	 * Data mapper
	 * @var \eMapper\Mapper
	 */
	protected $mapper;
	
	/**
	 * Entity profile
	 * @var \eMapper\Reflection\ClassProfile
	 */
	protected $entityProfile;
	
	public function __construct(Mapper $mapper, ClassProfile $classProfile = null) {
		$this->mapper = $mapper;
		$this->entityProfile = $classProfile;
	}
	
	/**
	 * Returns a new FluentSelect instance
	 * @param string $table
	 * @param string $alias
	 * @return \eMapper\Fluent\Query\FluentSelect
	 */
	public function from($table, $alias = null) {
		return new FluentSelect($this, $table, $alias);
	}
	
	/**
	 * Returns a new FluentInsert instance
	 * @param string $table
	 * @return \eMapper\Fluent\Query\FluentInsert
	 */
	public function insertInto($table) {
		return new FluentInsert($this, $table);
	}
	
	/**
	 * Returns a new FluentUpdate instance
	 * @param string $table
	 * @param string $alias
	 * @return \eMapper\Fluent\Query\FluentUpdate
	 */
	public function update($table, $alias = null) {
		return new FluentUpdate($this, $table, $alias);
	}
	
	/**
	 * Returns a new FluentDelete instance
	 * @param string $table
	 * @param string $alias
	 * @return \eMapper\Fluent\Query\FluentDelete
	 */
	public function deleteFrom($table, $alias = null) {
		return new FluentDelete($this, $table, $alias);
	}
	
	/**
	 * Obtains mapper instance
	 * @return \eMapper\Mapper
	 */
	public function getMapper() {
		return $this->mapper;
	}
	
	/**
	 * Obtains related entity profile
	 * @return \eMapper\Reflection\ClassProfile
	 */
	public function getEntityProfile() {
		return $this->entityProfile;
	}
}