<?php
namespace eMapper\Reflection\Profile\Association;

class ManyToOne extends AbstractAssociation {
	public function buildJoin($alias, $mainAlias) {
		return sprintf('INNER JOIN @@%s %s ON %s.%s = %s.%s',
					   $this->from->getReferredTable(), $alias,
					   $alias, $this->column,
					   $mainAlias, $this->from->getPrimaryKey(true));
	}
	
	protected function fetchValue($manager) {
		return $manager->get();
	}
}
?>