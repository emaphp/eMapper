<?php
namespace eMapper\SQLite;

use eMapper\AbstractEntityNamespaceTest;
use eMapper\Engine\SQLite\SQLiteDriver;
use eMapper\Mapper;
use Acme\Type\RGBColorTypeHandler;

class EntityNamespaceTest extends AbstractEntityNamespaceTest {
	public function build() {
		$this->driver = new SQLiteDriver(SQLiteTest::$filename);
		$this->mapper = new Mapper($this->driver);
		$this->mapper->addType('Acme\RGBColor', new RGBColorTypeHandler());
	}
}

?>