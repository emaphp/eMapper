<?php
namespace eMapper\Dynamic\Environment;

use eMacros\Environment\Environment;
use eMacros\Package\DatePackage;
use eMapper\Dynamic\Package\CorePackage;

class DynamicSQLEnvironment extends Environment {
	public function __construct() {
		$this->import(new DatePackage());
		$this->import(new CorePackage());
	}
}
?>