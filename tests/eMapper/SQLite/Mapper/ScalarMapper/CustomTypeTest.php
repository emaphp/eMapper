<?php
namespace eMapper\SQLite\Mapper\ScalarMapper;

use eMapper\SQLite\SQLiteConfig;
use eMapper\Mapper\ScalarMapper\AbstractCustomTypeTest;

/**
 * Test MySQLMapper with a custom type handler
 * @author emaphp
 * @group sqlite
 * @group mapper
 * @group custom
 */
class CustomTypeTest extends AbstractCustomTypeTest {
	use SQLiteConfig;
}
?>