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
	
	public abstract static function __callstatic($method, $args = null);
	public abstract function getColumnName(ClassProfile $profile);
	
	public function type($type) {
		$this->type = $type;
		return $this;
	}
	
	public function hasType() {
		return isset($this->type);
	}
	
	public function getType() {
		return $this->type;
	}
	
	public function eq($expression) {
		return new Equal($this, $expression);
	}
	
	public function contains($expression) {
		return new Contains($this, $expression);
	}
	
	public function in($expression) {
		return new In($this, $expression);
	}
	
	public function gt($expression) {
		return new GreaterThan($this, $expression);
	}
	
	public function gte($expression) {
		return new GreaterThanEqual($this, $expression);
	}
	
	public function lt($expression) {
		return new LessThan($this, $expression);
	}
	
	public function lte($expression) {
		return new LessThanEqual($this, $expression);
	}
	
	public function startswith($expression) {
		return new StartsWith($this, $expression);
	}
	
	public function endswith($expression) {
		return new EndsWith($this, $expression);
	}
	
	public function range($from, $to) {
		return new Range($this, $from, $to);
	}
	
	public function regex($expression) {
		return new Regex($this, $expression);
	}
}
?>