<?php
namespace eMapper\MySQL;

use eMapper\Mapper;
use eMapper\Engine\MySQL\MySQLDriver;
use Acme\Type\RGBColorTypeHandler;
use eMapper\Engine\MySQL\Statement\MySQLStatement;
use eMapper\Engine\MySQL\Type\MySQLTypeManager;
use eMapper\Engine\MySQL\Result\MySQLResultIterator;

trait MySQLConfig {
	protected $config = ['host' => '127.0.0.1', 'user' => 'root', 'password' => '', 'database' => 'emapper_testing'];
	
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
	
	protected function getStatement($conn) {
		return new MySQLStatement(new MySQLDriver($conn), new MySQLTypeManager());
	}
	
	protected function getBlob() {
		static $blob = null;
		if (is_null($blob)) $blob = file_get_contents(__DIR__ . '/../avatar.gif');
		return $blob;
	}
	
	protected function getPrefix() {
		return 'mysql_';
	}
}
?>