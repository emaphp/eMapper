<?php
namespace eMapper\Reflection\Profile\Association;

use eMapper\Manager;
use eMapper\Reflection\Profiler;
/**
 * The OneToMany class is an abstraction of onte-to-many associations.
 * @author emaphp
 */
class OneToMany extends AbstractAssociation {
	public function buildJoin($alias, $mainAlias) {
		$parentProfile = Profiler::getClassProfile($this->parent->getName());
		
		return sprintf('INNER JOIN @@%s %s ON %s.%s = %s.%s',
					   $parentProfile->getReferredTable(true), $alias,
					   $mainAlias, $this->foreignKey,
					   $alias, $parentProfile->getPrimaryKey(true));
	}
	
	protected function fetchValue(Manager $manager) {
		return $manager->find();
	}
}
?>