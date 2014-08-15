<?php
namespace eMapper\SQLite\Mapper\ScalarMapper;

use eMapper\SQLite\SQLiteConfig;
use eMapper\Mapper\ScalarMapper\AbstractIntegerTypeTest;

/**
 * Test SQLiteMapper with integer values
 * @author emaphp
 * @group sqlite
 * @group mapper
 * @group integer
 */
class IntegerTypeTest extends AbstractIntegerTypeTest {
	use SQLiteConfig;
}
?>