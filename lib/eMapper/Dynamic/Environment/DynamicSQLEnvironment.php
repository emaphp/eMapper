<?php
namespace eMapper\Dynamic\Environment;

use eMacros\Environment\Environment;
use eMacros\Package\RegexPackage;
use eMacros\Package\DatePackage;
use eMapper\Dynamic\Package\CorePackage;

/**
 * The DynamicSQLEnvironment class is the default environment class used for dynamic SQL
 * expressions and entity macros
 * @author emaphp
 */
class DynamicSQLEnvironment extends Environment {
	use \FluentConfiguration;

	/**
	 * Wrapper argument
	 * @var ParameterWrapper
	 */
	public $wrappedArgument;
	
	public function __construct() {
		$this->import(new RegexPackage());
		$this->import(new DatePackage());
		$this->import(new CorePackage());
	}
}
?>