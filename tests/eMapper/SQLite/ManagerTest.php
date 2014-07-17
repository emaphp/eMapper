<?php
namespace eMapper\SQLite;

use eMapper\AbstractManagerTest;
use eMapper\Engine\SQLite\SQLiteDriver;
use Acme\Type\RGBColorTypeHandler;
use eMapper\Mapper;

/**
 * SQLite manager test
 * @author emaphp
 * @group sqlite
 * @group manager
 */
class ManagerTest extends AbstractManagerTest {
	public function build() {
		$this->driver = new SQLiteDriver(SQLiteTest::$filename);
		$this->mapper = new Mapper($this->driver);
		$this->mapper->addType('Acme\RGBColor', new RGBColorTypeHandler());
		$this->productsManager = $this->mapper->buildManager('Acme\Entity\Product');
	}
}
?>