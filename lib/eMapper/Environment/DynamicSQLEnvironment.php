<?php
namespace eMapper\Environment;

use eMacros\Environment\Environment;
use eMacros\Package\ArrayPackage;
use eMacros\Package\StringPackage;
use eMacros\Package\CorePackage;
use eMacros\Package\DatePackage;

class DynamicSQLEnvironment extends Environment {
	public function __construct() {
		$this->import(new DatePackage);
		$this->import(new ArrayPackage);
		$this->import(new StringPackage);
		$this->import(new CorePackage);
	}
}
?>