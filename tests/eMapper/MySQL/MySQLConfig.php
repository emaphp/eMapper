<?php
namespace eMapper\MySQL;

use eMapper\Mapper;
use eMapper\Engine\MySQL\MySQLDriver;
use Acme\Type\RGBColorTypeHandler;
use eMapper\Engine\MySQL\Statement\MySQLStatement;
use eMapper\Engine\MySQL\Type\MySQLTypeManager;
use eMapper\Engine\MySQL\Result\MySQLResultIterator;

trait MySQLConfig {
	protected $config = ['host' => 'localhost', 'user' => 'root', 'password' => 'c4lpurn14', 'database' => 'emapper_testing'];
	
	protected function getConnection() {
		return new \mysqli($this->config['host'], $this->config['user'], $this->config['password'], $this->config['database']);
	}
	
	protected function getResultIterator($result) {
		return new MySQLResultIterator($result);
	}
	
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