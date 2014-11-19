<?php
namespace eMapper\SQL\Fluent;

use eMapper\Query\FluentQuery;

abstract class AbstractQuery {
	/**
	 * The parent query this instance is created from
	 * @var FluentQuery
	 */
	protected $fluent;
	
	public function __construct(FluentQuery $fluent) {
		$this->fluent = $fluent;
	}
	
	/**
	 * Returns the query as a sql string 
	 * @return string
	 */
	public abstract function build();
}
?>