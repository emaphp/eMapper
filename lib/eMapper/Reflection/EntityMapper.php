<?php
namespace eMapper\Reflection;

use eMapper\Reflection\Profile\ClassProfile;

/**
 * The EntityMapper trait provides additional methods use to build mapping expressions
 * given a class profile.
 * @author emaphp
 */
trait EntityMapper {
	protected function buildExpression(ClassProfile $entity) {
		return 'obj:' . $entity->reflectionClass->getName();
	}
	
	protected function buildListExpression(ClassProfile $entity, $index = null, $group = null) {
		$expr = 'obj:' . $entity->reflectionClass->getName();
		
		if (isset($group)) {
			$expr .= '<' . $group . '>';	
		}
		
		if (isset($index)) {
			$expr .= '[' . $index . ']';
		}
		else {
			$expr .= '[]';
		}
		
		return $expr;
	}
}
?>