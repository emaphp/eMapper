<?php
namespace eMapper\Reflection\Profile\Association;

use eMapper\Manager;
use eMapper\Reflection\Profiler;
/**
 * The OneToOne class is an abstraction of one-to-one associations.
 * @author emaphp
 */
class OneToOne extends AbstractAssociation {
	public function buildJoin($alias, $mainAlias) {
		$parentProfile = Profiler::getClassProfile($this->parent->getName());
		$entityProfile = Profiler::getClassProfile($this->profile->getName());
		
		if (isset($this->foreignKey)) {
			return sprintf('INNER JOIN @@%s %s ON %s.%s = %s.%s',
						   $parentProfile->getReferredTable(true), $alias,
						   $mainAlias, $this->foreignKey,
						   $alias, $parentProfile->getPrimaryKey(true));
		}
		elseif (isset($this->column)) {
			return sprintf('INNER JOIN @@%s %s ON %s.%s = %s.%s',
						   $parentProfile->getReferredTable(), $alias,
						   $alias, $entityProfile->getPrimaryKey(true),
						   $mainAlias, $this->column);
		}
	}
	
	protected function fetchValue(Manager $manager) {
		return $manager->get();
	}
}
?>