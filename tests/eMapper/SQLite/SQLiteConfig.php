<?php
namespace eMapper\SQLite;

use eMapper\Engine\SQLite\Type\SQLiteTypeManager;
use eMapper\Mapper;
use eMapper\Engine\SQLite\SQLiteDriver;
use Acme\Type\RGBColorTypeHandler;
use eMapper\Engine\SQLite\Statement\SQLiteStatement;
use eMapper\Engine\SQLite\Result\SQLiteResultIterator;

trait SQLiteConfig {
	protected function getFilename() {
		return __DIR__ . '/testing.db';
	}
	
	protected function getConnection() {
		return new \SQLite3($this->getFilename());
	}
	
	protected function getResultIterator($result) {
		return new SQLiteResultIterator($result);
	}
	
	protected function getStatement() {
		return new SQLiteStatement(new \SQLite3($this->getFilename()), new SQLiteTypeManager());
	}
	
	protected function getMapper() {
		$mapper = new Mapper(new SQLiteDriver($this->getFilename()));
		$mapper->addType('Acme\RGBColor', new RGBColorTypeHandler(), 'color');
		return $mapper;
	}
}
?>