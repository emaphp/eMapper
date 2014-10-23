<?php
namespace eMapper\Dynamic\Program;

use eMapper\Dynamic\Environment\DynamicSQLEnvironment;
use eMacros\Program\Program;
use eMapper\Reflection\Parameter\ParameterWrapper;

/**
 * The DynamicSQLProgram class executes Dynamic SQL expressions found in queries and
 * entity macros.
 * @author emaphp
 */
class DynamicSQLProgram extends Program {	
	public function executeWith(DynamicSQLEnvironment $env, array $args, $parameterMap = null) {
		//set wrapped argument in environment
		if (array_key_exists(0, $args) && (is_array($args[0]) || is_object($args[0])))
			$env->wrappedArgument = ParameterWrapper::wrapValue($args[0], $parameterMap);
		else
			$env->wrappedArgument = null;
		
		$env->arguments = $args;
		$value = null;
		
		foreach ($this->expressions as $expr)
			$value = $expr->evaluate($env); //store program result
		
		return $value;
	}
}
?>