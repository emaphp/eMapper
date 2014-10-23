<?php
namespace eMapper\SQL\Aggregate;

use eMapper\SQL\Statement;

/**
 * The StatementAggregate trait includes the methods to initialize a Statement instance.
 * @author emaphp
 */
trait StatementAggregate {
	/**
	 * Validates a given statement ID
	 * @param string $statementId
	 * @throws \InvalidArgumentException
	 */
	protected function validateStatementId($statementId) {
		//validate id
		if (!is_string($statementId) || !preg_match(Statement::STATEMENT_ID_REGEX, $statementId))
			throw new \InvalidArgumentException("Statement ID is not valid");
	}
}