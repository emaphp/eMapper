<?php
namespace eMapper\PostgreSQL\Mapper\ScalarMapper;

use eMapper\PostgreSQL\PostgreSQLConfig;
use eMapper\Mapper\ScalarMapper\AbstractIntegerTypeTest;

/**
 * Tests Mapper class with integer values
 * @author emaphp
 * @group postgre
 * @group mapper
 * @group integer
 */
class IntegerTypeTest extends AbstractIntegerTypeTest {
	use PostgreSQLConfig;
}

?>