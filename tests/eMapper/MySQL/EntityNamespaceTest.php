<?php

namespace eMapper\MySQL;

use eMapper\AbstractEntityNamespaceTest;
use eMapper\Engine\MySQL\MySQLDriver;
use eMapper\Mapper;
use Acme\Type\RGBColorTypeHandler;

/**
 * 
 * @author emaphp
 * @group mysql
 * @group namespace
 */
class EntityNamespaceTest extends AbstractEntityNamespaceTest {
	public function build() {
		$config = MySQLTest::$config;
		$this->driver = new MySQLDriver($config['database'], $config['host'], $config['user'], $config['password']);
		$this->mapper = new Mapper($this->driver);
		$this->mapper->addType('Acme\RGBColor', new RGBColorTypeHandler());
	}
}
?>