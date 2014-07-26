<?php
namespace eMapper\SQL\Builder;

class FindAllStatemetBuilder extends StatementBuilder {
	public function build($matches = null) {
		return sprintf("SELECT * FROM %s", $this->getTableName());
	}
}
?>