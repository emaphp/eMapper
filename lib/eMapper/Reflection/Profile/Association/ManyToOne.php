<?php
namespace eMapper\Reflection\Profile\Association;

/**
 * The ManyToOne class is an abstraction of many-to-one associations.
 * @author emaphp
 */
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