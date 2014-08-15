<?php
namespace eMapper\MySQL\Mapper\ScalarMapper;

use eMapper\MySQL\MySQLConfig;
use eMapper\Mapper\ScalarMapper\AbstractBooleanTypeTest;

/**
 * Test Mapper class with boolean values using a MySQLDriver
 * @author emaphp
 * @group mysql
 * @group mapper
 * @group boolean
 */
class BooleanTypeTest extends AbstractBooleanTypeTest {
	use MySQLConfig;
}
?>