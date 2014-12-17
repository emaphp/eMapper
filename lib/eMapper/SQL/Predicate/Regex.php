<?php
namespace eMapper\SQL\Predicate;

use eMapper\Engine\Generic\Driver;
use eMapper\Engine\Generic\Regex\GenericRegex;

/**
 * The Regex class represents the regular expression predicate.
 * @author emaphp
 */
class Regex extends StringComparisonPredicate {
	public function generate(Driver $driver) {
		$regex = $driver->getRegex();
		$regex->setOptions($this->caseSensitive, $this->negate);
		return $regex->getDynamicExpression(GenericRegex::REGEX);
	}
	
	protected function formatExpression(Driver $driver, $expression) {
		$regex = $driver->getRegex();
		$regex->setOptions($this->caseSensitive, $this->negate);
		return $regex->formatString($expression);
	}
	
	protected function buildComparisonExpression(Driver $driver) {
		$regex = $driver->getRegex();
		$regex->setOptions($this->caseSensitive, $this->negate);
		return $regex->getComparisonExpression(GenericRegex::REGEX);
	}
}