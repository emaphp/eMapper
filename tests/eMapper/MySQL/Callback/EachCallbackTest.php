<?php
namespace eMapper\MySQL\Callback;

use eMapper\MySQL\MySQLConfig;
use eMapper\Callback\AbstractEachCallbackTest;

/**
 * Tests applying a user-defined callback through the each method
 * 
 * @author emaphp
 * @group callback
 * @group mysql
 */
class EachCallbackTest extends AbstractEachCallbackTest {
	use MySQLConfig;
}
?>