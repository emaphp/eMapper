<?php
namespace eMapper\SQL\Predicate;

use eMapper\Engine\Generic\Driver;
use eMapper\Query\Cond;
use eMapper\SQL\Field\FieldTranslator;

/**
 * The Filter class is a container for various types of predicates.
 * @author emaphp
 */
class Filter extends SQLPredicate {
	/**
	 * Predicate list
	 * @var array
	 */
	protected $predicates;
	
	/**
	 * Operator
	 * @var string
	 */
	protected $operator;
	
	public function __construct($predicates, $negate = false, $operator = Cond::LOGICAL_AND) {
		$this->predicates = $predicates;
		$this->negate = $negate;
		$this->operator = $operator;
	}
	
	public function getPredicates() {
		return $this->predicates;
	}
	
	public function getOperator() {
		return $this->operator;
	}
	
	public function evaluate(FieldTranslator $translator, Driver $driver, array &$args, &$joins = null, $arg_index = 0) {
		if (empty($this->predicates))
			return '';

		if (count($this->predicates) == 1) {
			if (!empty($this->alias))
				$this->predicates[0]->setAlias($this->alias);
			$condition = $this->predicates[0]->evaluate($translator, $driver, $args, $joins, $arg_index);
			
			if ($this->negate)
				return 'NOT ' . $condition;
			return $condition;
		}
		
		$predicates = [];
		
		foreach ($this->predicates as $predicate) {
			if (!empty($this->alias))
				$predicate->setAlias($this->alias);
			$predicates[] = $predicate->evaluate($translator, $driver, $args, $joins, $arg_index);
		}
		
		$condition = '( ' . implode(" {$this->operator} ", $predicates) . ' )';
		
		if ($this->negate)
			return 'NOT ' . $condition;
		return $condition;
	}
	
	public function render(Driver $driver) {
		if (empty($this->predicates))
			return '';
		
		if (count($this->predicates) == 1) {
			$condition = $this->predicates[0]->render($driver);
			
			if ($this->negate)
				return 'NOT (' . $condition . ')';
			return $condition;
		}
		
		$predicates = [];
		
		foreach ($this->predicates as $predicate)
			$predicates[] = $predicate->render($driver);
		
		$condition = '( ' . implode(" {$this->operator} ", $predicates) . ' )';
		
		if ($this->negate)
			return 'NOT ' . $condition ;
		return $condition;
	}
}
?>