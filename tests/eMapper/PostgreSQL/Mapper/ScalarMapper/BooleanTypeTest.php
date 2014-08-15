<?php
namespace eMapper\PostgreSQL\Mapper\ScalarMapper;

use eMapper\PostgreSQL\PostgreSQLConfig;
use eMapper\Mapper\ScalarMapper\AbstractBooleanTypeTest;

/**
 * Test Mapper class with boolean values using a PostgreSQLDriver
 * @author emaphp
 * @group postgre
 * @group mapper
 * @group boolean
 */
class BooleanTypeTest extends AbstractBooleanTypeTest {
	use PostgreSQLConfig;
}
?>