<?php
namespace eMapper\MySQL\Callback;

use eMapper\MySQL\MySQLConfig;
use eMapper\Callback\AbstractOverrideCallbackTest;

/**
 * Test setting a callback that overrides current query
 * 
 * @author emaphp
 * @group callback
 * @group mysql
 */
class OverrideCallbackTest extends AbstractOverrideCallbackTest {
	use MySQLConfig;
}
?>