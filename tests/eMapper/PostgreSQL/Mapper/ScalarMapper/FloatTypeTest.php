<?php
namespace eMapper\PostgreSQL\Mapper\ScalarMapper;

use eMapper\PostgreSQL\PostgreSQLConfig;
use eMapper\Mapper\ScalarMapper\AbstractFloatTypeTest;

/**
 * Tests Mapper class with float values
 * @author emaphp
 * @group postgre
 * @group mapper
 * @group float
 */
class FloatTypeTest extends AbstractFloatTypeTest {
	use PostgreSQLConfig;
}
?>