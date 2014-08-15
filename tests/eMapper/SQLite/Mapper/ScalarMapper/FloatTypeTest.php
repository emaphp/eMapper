<?php
namespace eMapper\SQLite\Mapper\ScalarMapper;

use eMapper\SQLite\SQLiteConfig;
use eMapper\Mapper\ScalarMapper\AbstractFloatTypeTest;

/**
 * Test SQLiteMapper with float values
 * @author emaphp
 * @group sqlite
 * @group mapper
 * @group float
 */
class FloatTypeTest extends AbstractFloatTypeTest {
	use SQLiteConfig;
}
?>