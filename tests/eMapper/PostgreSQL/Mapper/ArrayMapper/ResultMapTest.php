<?php
namespace eMapper\PostgreSQL\Mapper\ArrayMapper;

use eMapper\PostgreSQL\PostgreSQLConfig;
use eMapper\Mapper\ArrayMapper\AbstractResultMapTest;

/**
 * Tests Mapper class obtaining array values using result maps
 * @author emaphp
 * @group postgre
 * @group mapper
 */
class ResultMapTest extends AbstractResultMapTest {
	use PostgreSQLConfig;	
}
?>