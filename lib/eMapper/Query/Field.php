<?php
namespace eMapper\Query;

use eMapper\Reflection\Profile\ClassProfile;
use eMapper\Query\Predicate\Equal;
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

/**
 * The Field class represents an entity attribute or table column.
 * @author emaphp
 */
abstract class Field {
	/**
	 * Column/Attribute name
	 * @var string
	 */
	protected $name;
	
	/**
	 * Field associated type
	 * @var string
	 */
	protected $type;
	
	/**
	 * Field path
	 * @var array
	 */
	protected $path;
	
	public function __construct($name, $type = null) {
		if (strstr($name, '__')) {
			$this->path = explode('__', $name);
			$this->name = array_pop($this->path);
		}
		else {
			$this->name = $name;
		}
		 
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
	
	/**
	 * Obtains field's name
	 * @return string
	 */
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
	
	/**
	 * Obtains field full path
	 * @return NULL|string
	 */
	public function getFullPath() {
		if (is_null($this->path)) {
			return null;
		}
	
		return implode('_', $this->path);
	}
	
	/**
	 * Obtains field associations as a list
	 * @param ClassProfile $profile
	 * @param string $return_profile
	 * @throws \RuntimeException
	 * @return mixed
	 */
	public function getAssociations(ClassProfile $profile, $return_profile = true) {
		if (is_null($this->path)) {
			if ($return_profile) {
				return [null, null];
			}
				
			return null;
		}
	
		$associations = [];
		$current = $profile;
	
		for ($i = 0; $i < count($this->path); $i++) {
			//build name
			$name = implode('_', array_slice($this->path, 0, $i + 1));
				
			$property = $this->path[$i];
			$association = $current->getAssociation($property);
	
			if ($association === false) {
				throw new \RuntimeException(sprintf("Association '%s' not found in class %s", $property, $current->getReflectionClass()->getName()));
			}
				
			$associations[$name] = $association;
			$current = $association->getProfile();
		}
	
		if ($return_profile) {
			return [$associations, $current];
		}
	
		return $associations;
	}
	
	/**
	 * Obtains the last referred profile of the current field
	 * @param ClassProfile $profile
	 * @throws \RuntimeException
	 * @return ClassProfile
	 */
	protected function getReferredProfile(ClassProfile $profile) {
		$current = $profile;
		
		foreach ($this->path as $property) {
			$association = $current->getAssociation($property);
				
			if ($association === false) {
				throw new \RuntimeException(sprintf("Association '%s' not found in class %s", $property, $current->getReflectionClass()->getName()));
			}
				
			$current = $association->getProfile();
		}
		
		return $current;
	}
	
	/*
	 * PREDICATES
	 */
	
	/**
	 * Returns an Equal predicate for the current field
	 * @param mixed $expression
	 * @param boolean $condition
	 * @return \eMapper\Query\Predicate\Equal
	 */
	public function eq($expression, $condition = true) {
		$eq = new Equal($this, !$condition);
		$eq->setExpression($expression);
		return $eq;
	}
	
	/**
	 * Return a Contains predicate for the current field
	 * @param string $expression
	 * @param boolean $condition
	 * @return \eMapper\Query\Predicate\Contains
	 */
	public function contains($expression, $condition = true) {
		$contains = new Contains($this, true, !$condition);
		$contains->setExpression($expression);
		return $contains;
	}
	
	/**
	 * Returns an case-insentive Contains predicate for the current field
	 * @param string $expression
	 * @param boolean $condition
	 * @return \eMapper\Query\Predicate\Contains
	 */
	public function icontains($expression, $condition = true) {
		$icontains = new Contains($this, false, !$condition);
		$icontains->setExpression($expression);
		return $icontains;
	}
	
	/**
	 * Returns an In predicate for the current field
	 * @param array $expression
	 * @param boolean $condition
	 * @return \eMapper\Query\Predicate\In
	 */
	public function in($expression, $condition = true) {
		$in = new In($this, !$condition);
		$in->setExpression($expression);
		return $in;
	}
	
	/**
	 * Returns a GreaterThan predicate for the current field
	 * @param mixed $expression
	 * @param boolean $condition
	 * @return \eMapper\Query\Predicate\GreaterThan
	 */
	public function gt($expression, $condition = true) {
		$gt = new GreaterThan($this, !$condition);
		$gt->setExpression($expression);
		return $gt;
	}
	
	/**
	 * Returns a GreaterThanEqual predicate for the current field
	 * @param mixed $expression
	 * @param boolean $condition
	 * @return \eMapper\Query\Predicate\GreaterThanEqual
	 */
	public function gte($expression, $condition = true) {
		$gte = new GreaterThanEqual($this, !$condition);
		$gte->setExpression($expression);
		return $gte;
	}
	
	/**
	 * Returns a LessThan predicate for the current field
	 * @param mixed $expression
	 * @param boolean $condition
	 * @return \eMapper\Query\Predicate\LessThan
	 */
	public function lt($expression, $condition = true) {
		$lt = new LessThan($this, !$condition);
		$lt->setExpression($expression);
		return $lt;
	}
	
	/**
	 * Returns a LessThanEqual predicate for the current field
	 * @param mixed $expression
	 * @param boolean $condition
	 * @return \eMapper\Query\Predicate\LessThanEqual
	 */
	public function lte($expression, $condition = true) {
		$lte = new LessThanEqual($this, !$condition);
		$lte->setExpression($expression);
		return $lte;
	}
	
	/**
	 * Returns a StartsWith predicate for the current field
	 * @param string $expression
	 * @param boolean $condition
	 * @return \eMapper\Query\Predicate\StartsWith
	 */
	public function startswith($expression, $condition = true) {
		$startswith = new StartsWith($this, true, !$condition);
		$startswith->setExpression($expression);
		return $startswith;
	}
	
	/**
	 * Returns a case-insensitive StartsWith predicate for the current field
	 * @param string $expression
	 * @param boolean $condition
	 * @return \eMapper\Query\Predicate\StartsWith
	 */
	public function istartswith($expression, $condition = true) {
		$istartswith = new StartsWith($this, false, !$condition);
		$istartswith->setExpression($expression);
		return $istartswith;
	}
	
	/**
	 * Returns a EndsWith predicate for the current field
	 * @param string $expression
	 * @param boolean $condition
	 * @return \eMapper\Query\Predicate\EndsWith
	 */
	public function endswith($expression, $condition = true) {
		$endswith = new EndsWith($this, true, !$condition);
		$endswith->setExpression($expression);
		return $endswith;
	}
	
	/**
	 * Returns a case-insensitive EndsWith predicate for the current field
	 * @param string $expression
	 * @param boolean $condition
	 * @return \eMapper\Query\Predicate\EndsWith
	 */
	public function iendswith($expression, $condition = true) {
		$iendswith = new EndsWith($this, false, !$condition);
		$iendswith->setExpression($expression);
		return $iendswith;
	}
	
	/**
	 * Returns a Range predicate for the current field
	 * @param mixed $from
	 * @param mixed $to
	 * @param boolean $condition
	 * @return \eMapper\Query\Predicate\Range
	 */
	public function range($from, $to, $condition = true) {
		$range = new Range($this, !$condition);
		$range->setFrom($from);
		$range->setTo($to);
		return $range;
	}
	
	/**
	 * Returns a Regex predicate for the current field
	 * @param string $expression
	 * @param boolean $condition
	 * @return \eMapper\Query\Predicate\Regex
	 */
	public function matches($expression, $condition = true) {
		$matches = new Regex($this, true, !$condition);
		$matches->setExpression($expression);
		return $matches;
	}
	
	/**
	 * Returns a case-insensitive Regex predicate for the current field
	 * @param string $expression
	 * @param boolean $condition
	 * @return \eMapper\Query\Predicate\Regex
	 */
	public function imatches($expression, $condition = true) {
		$imatches = new Regex($this, false, !$condition);
		$imatches->setExpression($expression);
		return $imatches;
	}
	
	/**
	 * Returns a IsNull predicate for the current field
	 * @param string $condition
	 * @return \eMapper\Query\Predicate\IsNull
	 */
	public function isnull($condition = true) {
		return new IsNull($this, !$condition);
	}
}
?>