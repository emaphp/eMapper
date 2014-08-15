<?php
namespace eMapper\PostgreSQL\Mapper\ScalarMapper;

use eMapper\PostgreSQL\PostgreSQLConfig;
use eMapper\Mapper\ScalarMapper\AbstractDatetimeTypeTest;

/**
 * Test Mapper class with date values
 * @author emaphp
 * @group postgre
 * @group mapper
 * @group date
 */
class DatetimeTypeTest extends AbstractDatetimeTypeTest {
	use PostgreSQLConfig;
}
?>