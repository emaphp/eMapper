<?php
namespace eMapper\SQLite;

use eMapper\Engine\SQLite\Type\SQLiteTypeManager;
use eMapper\Mapper;
use eMapper\Engine\SQLite\SQLiteDriver;
use Acme\Type\RGBColorTypeHandler;
use eMapper\Engine\SQLite\Statement\SQLiteStatement;

trait SQLiteConfig {
	protected function getFilename() {
		return __DIR__ . '/testing.db';
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