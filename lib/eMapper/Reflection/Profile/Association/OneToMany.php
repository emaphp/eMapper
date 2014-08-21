<?php
namespace eMapper\Reflection\Profile\Association;

/**
 * The OneToMany class is an abstraction of onte-to-many associations.
 * @author emaphp
 */
class OneToMany extends AbstractAssociation {
	public function buildJoin($alias, $mainAlias) {
		return sprintf('INNER JOIN @@%s %s ON %s.%s = %s.%s',
					   $this->from->getReferredTable(true), $alias,
					   $mainAlias, $this->foreignKey,
					   $alias, $this->from->getPrimaryKey(true));
	}
	
	protected function fetchValue($manager) {
		return $manager->find();
	}
}
?>