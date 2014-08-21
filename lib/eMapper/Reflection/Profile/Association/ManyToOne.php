<?php
namespace eMapper\Reflection\Profile\Association;

use eMapper\Manager;
use eMapper\Reflection\Profiler;
/**
 * The ManyToOne class is an abstraction of many-to-one associations.
 * @author emaphp
 */
class ManyToOne extends AbstractAssociation {
	public function buildJoin($alias, $mainAlias) {
		$parentProfile = Profiler::getClassProfile($this->parent->getName());
		$entityProfile = Profiler::getClassProfile($this->profile->getName());
		
		return sprintf('INNER JOIN @@%s %s ON %s.%s = %s.%s',
					   $parentProfile->getReferredTable(), $alias,
					   $alias, $this->column,
					   $mainAlias, $entityProfile->getPrimaryKey(true));
	}
	
	protected function fetchValue(Manager $manager) {
		return $manager->get();
	}
}
?>