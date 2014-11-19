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
	use PostgreSQLConfig;
}
?>