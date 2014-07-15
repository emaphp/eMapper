<?php
namespace eMapper\Query;

use eMapper\Query\Predicate\Equal;
use eMapper\Reflection\Profile\ClassProfile;
use eMapper\Query\Predicate\Contains;
use eMapper\Query\Predicate\In;
use eMapper\Query\Predicate\GreaterThan;
use eMapper\Query\Predicate\GreaterThanEqual;
use eMapper\Query\Predicate\LessThan;
use eMapper\Query\Predicate\LessThanEqual;
use eMapper\Query\Predicate\StartsWith;
use eMapper\Query\Predicate\EndsWith;
use eMapper\Query\Predicate\Range;
use eMapper\Query\Predicate\Regex;
use eMapper\Query\Predicate\IsNull;

abstract class Field {
	/**
	 * Column/Attribute nam
	 * @var string
	 */
	protected $name;
	
	/**
	 * Field associated type
	 * @var string
	 */
	protected $type;
	
	public function __construct($name) {
		$this->name = $name;
	}
	
	/**
	 * Initializes a new Field instance
	 * @param string $method
	 * @param null $args
	 */
	public abstract static function __callstatic($method, $args = null);
	
	/**
	 * Obtains the referenced column of this field
	 * @param ClassProfile $profile
	 */
	public abstract function getColumnName(ClassProfile $profile);
	
	public function getName() {
		return $this->name;
	}
	
	/**
	 * Sets field type
	 * @param string $type
	 * @return \eMapper\Query\Field
	 */
	public function type($type) {
		$this->type = $type;
		return $this;
	}
	
	/**
	 * Determines if the field has an associated type
	 * @return boolean
	 */
	public function hasType() {
		return isset($this->type);
	}
	
	/**
	 * Obtains current field type
	 * @return string
	 */
	public function getType() {
		return $this->type;
	}
	
	/*
	 * PREDICATES
	 */
	
	public function eq($expression, $condition = true) {
		return new Equal($this, $expression, !$condition);
	}
	
	public function contains($expression, $condition = true) {
		return new Contains($this, $expression, true, !$condition);
	}
	
	public function icontains($expression, $condition = true) {
		return new Contains($this, $expression, false, !$condition);
	}
	
	public function in($expression, $condition = true) {
		return new In($this, $expression, !$condition);
	}
	
	public function gt($expression, $condition = true) {
		return new GreaterThan($this, $expression, !$condition);
	}
	
	public function gte($expression, $condition = true) {
		return new GreaterThanEqual($this, $expression, !$condition);
	}
	
	public function lt($expression, $condition = true) {
		return new LessThan($this, $expression, !$condition);
	}
	
	public function lte($expression, $condition = true) {
		return new LessThanEqual($this, $expression, !$condition);
	}
	
	public function startswith($expression, $condition = true) {
		return new StartsWith($this, $expression, true, !$condition);
	}
	
	public function istartswith($expression, $condition = true) {
		return new StartsWith($this, $expression, false, !$condition);
	}
	
	public function endswith($expression, $condition = true) {
		return new EndsWith($this, $expression, true, !$condition);
	}
	
	public function iendswith($expression, $condition = true) {
		return new EndsWith($this, $expression, false, !$condition);
	}
	
	public function range($from, $to, $condition = true) {
		return new Range($this, $from, $to, !$condition);
	}
	
	public function regex($expression, $condition = true) {
		return new Regex($this, $expression, true, !$condition);
	}
	
	public function iregex($expression, $condition = true) {
		return new Regex($this, $expression, false, !$condition);
	}
	
	public function isnull($condition = true) {
		return new IsNull($field, !$condition);
	}
}
?>