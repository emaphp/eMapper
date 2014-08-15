<?php
namespace eMapper\PostgreSQL\Mapper\ScalarMapper;

use eMapper\PostgreSQL\PostgreSQLConfig;
use eMapper\Mapper\ScalarMapper\AbstractCustomTypeTest;

/**
 * Tests Mapper with custom type values
 * @author emaphp
 * @group postgre
 * @group mapper
 * @group custom
 */
class CustomTypeTest extends AbstractCustomTypeTest {
	use PostgreSQLConfig;
}
?>