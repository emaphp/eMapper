<?php
namespace eMapper\PostgreSQL;

use eMapper\AbstractEntityNamespaceTest;
use eMapper\Engine\PostgreSQL\PostgreSQLDriver;
use eMapper\Mapper;
use Acme\Type\RGBColorTypeHandler;

/**
 * 
 * @author emaphp
 * @group postgre
 * @group namespace
 */
class EntityNamespaceTest extends AbstractEntityNamespaceTest {
	public function build() {
		$connection_string = PostgreSQLTest::$connstring;
		$this->driver = new PostgreSQLDriver($connection_string);
		$this->mapper = new Mapper($this->driver);
		$this->mapper->addType('Acme\RGBColor', new RGBColorTypeHandler());
	}
}

?>