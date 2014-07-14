<?php
namespace eMapper\Query\Predicate;

use eMapper\Query\Field;
use eMapper\Reflection\Profile\ClassProfile;

class Equal extends SQLPredicate {
	/**
	 * Left expression
	 * @var Field
	 */
	protected $left_expression;
	
	/**
	 * Right expression
	 * @var mixed
	 */
	protected $right_expression;
	
	public function __construct(Field $left_expr, $right_expr) {
		$this->left_expression = $left_expr;
		$this->right_expression = $right_expr;
	}
	
	public function evaluate(ClassProfile $profile, &$args, $arg_index = 0) {
		$left_expr = $this->left_expression->getColumnName($profile);
		
		if ($this->right_expression instanceof Field) {
			$right_expr = $this->right_expression->getColumnName($profile);
		}
		else {
			if ($arg_index != 0) {
				$index = self::argNumber();
				$args[$index] = $this->right_expression;
					
				if (isset($this->left_expression->hasType())) {
					$index = $arg_index . '[' . $index . ':' . $this->left_expression->getType() . ']';
				}
				else {
					$type = $profile->getColumnType($left_expr);
				
					if (isset($type)) {
						$index = $arg_index . '[' . $index . ':' . $type .  ']';
					}
					else {
						$index = $arg_index . '[' . $index . ']';
					}
				}
					
				$right_expr = '%{' . $index . '}';
			}
			else {
				$index = 'arg' . self::argNumber();
				$args[$index] = $this->right_expression;
					
				if (isset($this->left_expression->hasType())) {
					$index = $index . ':' . $this->left_expression->getType();
				}
				else {
					$type = $profile->getColumnType($left_expr);
				
					if (isset($type)) {
						$index = $index . ':' . $type;
					}
				}
					
				$right_expr = '#{' . $index . '}';
			}
		}
				
		$expr = $left_expr . ' = ' . $right_expr;
		
		if ($this->negate) {
			$expr = 'NOT ' . $expr;
		}
		
		return $expr;
	}
}
?>