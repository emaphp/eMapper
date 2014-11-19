<?php
namespace eMapper\MySQL;

use eMapper\AbstractManagerTest;
use eMapper\Mapper;
use eMapper\Engine\MySQL\MySQLDriver;
use Acme\Type\RGBColorTypeHandler;

/**
 * MySQL manager test
 * @author emaphp
 * @group mysql
 * @group manager
 */
class ManagerTest extends AbstractManagerTest {
	use MySQLConfig;
}	
?>