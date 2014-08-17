<?php
namespace eMapper\Reflection\Profile\Association;

class OneToOne extends AbstractAssociation {
	public function buildJoin($alias, $mainAlias) {
		if (isset($this->foreignKey)) {
			return sprintf('INNER JOIN @@%s %s ON %s.%s = %s.%s',
						   $this->from->getReferredTable(true), $alias,
						   $mainAlias, $this->foreignKey,
						   $alias, $this->from->getPrimaryKey(true));
		}
		elseif (isset($this->column)) {
			return sprintf('INNER JOIN @@%s %s ON %s.%s = %s.%s',
						   $this->from->getReferredTable(), $alias,
						   $alias, $this->from->getPrimaryKey(true),
						   $mainAlias, $this->column);
		}
	}
	
	protected function fetchValue($manager) {
		return $manager->get();
	}
}
?>