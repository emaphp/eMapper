<?php
namespace eMapper\SQLite\Mapper\ScalarMapper;

use eMapper\SQLite\SQLiteConfig;
use eMapper\Mapper\ScalarMapper\AbstractStringTypeTest;

/**
 * Test SQLiteMapper with string values
 * @author emaphp
 * @group sqlite
 * @group mapper
 * @group string
 */
class StringTypeTest extends AbstractStringTypeTest {
	use SQLiteConfig;
}
?>