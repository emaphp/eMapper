<?php
namespace eMapper\SQLite;

use eMapper\Engine\SQLite\Statement\SQLiteStatement;
use eMapper\Engine\SQLite\Type\SQLiteTypeManager;
use eMapper\AbstractDynamicSQLTest;
use eMapper\Mapper;
use eMapper\Engine\SQLite\SQLiteDriver;
use Acme\Type\RGBColorTypeHandler;

/**
 * Test dynamix sql expressions running in a SQLite environment
 * @author emaphp
 * @group sqlite
 * @group dynamic
 */
class DynamicSQLTest extends AbstractDynamicSQLTest {
	use SQLiteConfig;
	
	public function buildStatement() {
		$this->statement = $this->getStatement();
	}
	
	public function buildMapper() {
		$this->mapper = $this->getMapper();
	}
}
?>