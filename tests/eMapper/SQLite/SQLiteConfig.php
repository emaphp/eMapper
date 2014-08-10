<?php
namespace eMapper\SQLite;

trait SQLiteConfig {
	public function getFilename() {
		return __DIR__ . '/testing.db';
	} 
}
?>