<?php
namespace eMapper\SQLite;

use eMapper\SQLite\SQLiteConfig;
use eMapper\MapperTest;
use Acme\Storage\User;
use Acme\Storage\Profile;
use eMapper\Query\Attr;
use Acme\Storage\Client;
use Acme\Storage\Pet;
use Acme\Storage\Driver;
use Acme\Storage\Car;
use Acme\Storage\Task;
use Acme\Storage\Employee;
use Acme\Storage\Person;
use Acme\Storage\Address;

/**
 * 
 * @author emaphp
 * @group storage
 */
abstract class StorageTest extends MapperTest {
	use SQLiteConfig;
	
	protected function getFilename() {
		return __DIR__ . '/storage.db';
	}
		
	protected function truncateTable($table) {
		$mapper = $this->getMapper();
		$mapper->newQuery()->deleteFrom($table)->exec();
		$mapper->close();
	}
}