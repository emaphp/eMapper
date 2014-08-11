<?php
namespace eMapper\SQLite\Callback;

use eMapper\SQLite\SQLiteConfig;
use eMapper\Callback\AbstractOverrideCallbackTest;

/**
 * Tests setting a callback that overrides a query
 * @author emaphp
 * @group sqlite
 * @group callback
 */
class OverrideCallbackTest extends AbstractOverrideCallbackTest {
	use SQLiteConfig;
}
?>