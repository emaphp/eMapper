<?php
namespace eMapper\Reflection\Profile\Association;

/**
 * The ManyToMany class is an abstraction of many-to-many associations.
 * @author emaphp
 */
class ManyToMany extends AbstractAssociation {
	public function buildJoin($alias, $mainAlias) {
		$joinTable = $this->joinWith->getArgument();
		$joinTableAlias = "$alias_$mainAlias";
		$joinTableColumn = $this->joinWith->getValue();
		$foreignKey = $this->foreignKey;
		
		return sprintf('INNER JOIN @@%s %s ON %s.%s = %s.%s INNER JOIN @@%s %s ON %s.%s = %s.%s',
						$joinTable, $joinTableAlias,
						$joinTableAlias, $joinTableColumn,
						$mainAlias, $this->profile->getPrimaryKey(true),
						$this->from->getReferredTable(), $alias,
						$joinTableAlias, $this->foreignKey,
						$alias, $this->from->getPrimaryKey(true));
	}
	
	protected function fetchValue($manager) {
		return $manager->find();
	}
}
?>