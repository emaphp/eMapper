<?php
namespace eMapper\MySQL;

use eMapper\Mapper;
use eMapper\Engine\MySQL\MySQLDriver;
use Acme\Type\RGBColorTypeHandler;
use eMapper\Engine\MySQL\Statement\MySQLStatement;
use eMapper\Engine\MySQL\Type\MySQLTypeManager;

trait MySQLConfig {
	protected $config = ['host' => 'localhost', 'user' => 'root', 'password' => 'c4lpurn14', 'database' => 'emapper_testing'];
	
	protected function getMapper() {
		$mapper = new Mapper(new MySQLDriver($this->config['database'], $this->config['host'], $this->config['user'], $this->config['password']));
		$mapper->addType('Acme\RGBColor', new RGBColorTypeHandler(), 'color');
		return $mapper;
	}
	
	protected function getStatement() {
		$conn = new \mysqli($this->config['host'], $this->config['user'], $this->config['password'], $this->config['database']);
		return new MySQLStatement($conn, new MySQLTypeManager());
	}
}
?>