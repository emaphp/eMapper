<?php
namespace eMapper\SQLite\Callback;

use eMapper\SQLite\SQLiteConfig;
use eMapper\Callback\AbstractDebugCallbackTest;

/**
 * Test setting a debug callback for the current query
 * @author emaphp
 * @group sqlite
 * @group callback
 */
class DebugCallbackTest extends AbstractDebugCallbackTest {
	use SQLiteConfig;
}
?>