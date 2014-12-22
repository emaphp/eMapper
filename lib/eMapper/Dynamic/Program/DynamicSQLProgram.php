<?php
namespace eMapper\Dynamic\Program;

use eMacros\Program\Program;
use eMapper\Dynamic\Environment\DynamicSQLEnvironment;
use eMapper\Reflection\Argument\ArgumentWrapper;
use eMacros\Environment\Environment;

/**
 * The DynamicSQLProgram class executes Dynamic SQL expressions found in queries and entity macros.
 * @author emaphp
 */
class DynamicSQLProgram extends Program {
	public function execute(Environment $env) {
		$env->arguments = array_slice(func_get_args(), 1);
		
		//wrap first argument
		if (array_key_exists(0, $env->arguments) && (is_array($env->arguments[0]) || is_object($env->arguments[0])))
			$env->wrappedArgument = ArgumentWrapper::wrap($env->arguments[0]);
			
		$value = null;
		
		foreach ($this->expressions as $expr)
			$value = $expr->evaluate($env);
		
		return $value;
	}
}
