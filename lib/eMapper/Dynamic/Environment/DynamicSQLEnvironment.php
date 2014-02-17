<?php
namespace eMapper\Dynamic\Environment;

use eMacros\Environment\Environment;
use eMapper\Dynamic\Environment\ConfigurableEnvironment;
use eMacros\Package\RegexPackage;
use eMacros\Package\DatePackage;
use eMapper\Dynamic\Package\CorePackage;

class DynamicSQLEnvironment extends Environment {
	use ConfigurableEnvironment;

	public function __construct() {
		$this->import(new RegexPackage());
		$this->import(new DatePackage());
		$this->import(new CorePackage());
	}
}
?>