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
	
	public function __construct($name, $type = null) {
		$this->name = $name;
		$this->type = $type;
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
		$eq = new Equal($this, !$condition);
		$eq->setExpression($expression);
		return $eq;
	}
	
	public function contains($expression, $condition = true) {
		$contains = new Contains($this, true, !$condition);
		$contains->setExpression($expression);
		return $contains;
	}
	
	public function icontains($expression, $condition = true) {
		$icontains = new Contains($this, false, !$condition);
		$icontains->setExpression($expression);
		return $icontains;
	}
	
	public function in($expression, $condition = true) {
		$in = new In($this, !$condition);
		$in->setExpression($expression);
		return $in;
	}
	
	public function gt($expression, $condition = true) {
		$gt = new GreaterThan($this, !$condition);
		$gt->setExpression($expression);
		return $gt;
	}
	
	public function gte($expression, $condition = true) {
		$gte = new GreaterThanEqual($this, !$condition);
		$gte->setExpression($expression);
		return $gte;
	}
	
	public function lt($expression, $condition = true) {
		$lt = new LessThan($this, !$condition);
		$lt->setExpression($expression);
		return $lt;
	}
	
	public function lte($expression, $condition = true) {
		$lte = new LessThanEqual($this, !$condition);
		$lte->setExpression($expression);
		return $lte;
	}
	
	public function startswith($expression, $condition = true) {
		$startswith = new StartsWith($this, true, !$condition);
		$startswith->setExpression($expression);
		return $startswith;
	}
	
	public function istartswith($expression, $condition = true) {
		$istartswith = new StartsWith($this, false, !$condition);
		$istartswith->setExpression($expression);
		return $istartswith;
	}
	
	public function endswith($expression, $condition = true) {
		$endswith = new EndsWith($this, true, !$condition);
		$endswith->setExpression($expression);
		return $endswith;
	}
	
	public function iendswith($expression, $condition = true) {
		$iendswith = new EndsWith($this, false, !$condition);
		$iendswith->setExpression($expression);
		return $iendswith;
	}
	
	public function range($from, $to, $condition = true) {
		$range = new Range($this, !$condition);
		$range->setFrom($from);
		$range->setTo($to);
		return $range;
	}
	
	public function matches($expression, $condition = true) {
		$matches = new Regex($this, true, !$condition);
		$matches->setExpression($expression);
		return $matches;
	}
	
	public function imatches($expression, $condition = true) {
		$imatches = new Regex($this, false, !$condition);
		$imatches->setExpression($expression);
		return $imatches;
	}
	
	public function isnull($condition = true) {
		return new IsNull($this, !$condition);
	}
}
?>