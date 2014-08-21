<?php
namespace eMapper\Reflection\Profile\Association;

use eMapper\Manager;
use eMapper\Reflection\Profiler;
/**
 * The ManyToMany class is an abstraction of many-to-many associations.
 * @author emaphp
 */
class ManyToMany extends AbstractAssociation {
	public function buildJoin($alias, $mainAlias) {
		$parentProfile = Profiler::getClassProfile($this->parent->getName());
		$entityProfile = Profiler::getClassProfile($this->profile->getName());
		
		$joinTable = $this->joinWith->getArgument();
		$joinTableAlias = $alias . '_' . $mainAlias;
		$joinTableColumn = $this->joinWith->getValue();
		$foreignKey = $this->foreignKey;
		
		return sprintf('INNER JOIN @@%s %s ON %s.%s = %s.%s INNER JOIN @@%s %s ON %s.%s = %s.%s',
						$joinTable, $joinTableAlias,
						$joinTableAlias, $joinTableColumn,
						$mainAlias, $entityProfile->getPrimaryKey(true),
						$parentProfile->getReferredTable(), $alias,
						$joinTableAlias, $this->foreignKey,
						$alias, $parentProfile->getPrimaryKey(true));
	}
	
	protected function fetchValue(Manager $manager) {
		return $manager->find();
	}
}
?>