<?php
namespace eMapper\PostgreSQL;

use eMapper\AbstractManagerTest;
use eMapper\Engine\PostgreSQL\PostgreSQLDriver;
use Acme\Type\RGBColorTypeHandler;
use eMapper\Mapper;

/**
 * PostgreSQL manager test
 * @author emaphp
 * @group postgre
 * @group manager
 */
class ManagerTest extends AbstractManagerTest {
	public function build() {
		$connection_string = PostgreSQLTest::$connstring;
		$this->driver = new PostgreSQLDriver($connection_string);
		$this->mapper = new Mapper($this->driver);
		$this->mapper->addType('Acme\RGBColor', new RGBColorTypeHandler());
		$this->productsManager = $this->mapper->buildManager('Acme\Entity\Product');
	}
}
?>