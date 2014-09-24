<?php
namespace eMapper\MySQL\Callback;

use eMapper\MySQL\MySQLConfig;
use eMapper\Callback\AbstractDebugCallbackTest;

/**
 * Test setting a debug callback for the current query
 * 
 * @author emaphp
 * @group callback
 * @group mysql
 */
class DebugCallbackTest extends AbstractDebugCallbackTest {
	use MySQLConfig;
}
?>