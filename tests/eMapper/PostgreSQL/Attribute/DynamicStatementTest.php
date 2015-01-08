<?php
namespace eMapper\PostgreSQL\Attribute;

use eMapper\Attribute\AbstractDynamicStatementTest;
use eMapper\PostgreSQL\PostgreSQLConfig;

/**
 * @group attribute
 * @group postgre
 */
class DynamicStatementTest extends AbstractDynamicStatementTest {
	use PostgreSQLConfig;
}
