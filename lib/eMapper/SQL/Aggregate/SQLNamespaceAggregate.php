<?php
namespace eMapper\SQL\Aggregate;

use eMapper\SQL\SQLNamespace;
use eMapper\SQL\Statement;
use eMapper\SQL\Configuration\StatementConfigurationContainer;
use eMapper\SQL\Aggregate\StatementAggregate;

/**
 * The SQLNamespaceAggregate trait includes the methods to initialize a SQLNamespace instance. 
 * @author emaphp
 */
trait SQLNamespaceAggregate {
	use StatementAggregate;
	
	/**
	 * Inner namespaces
	 * @var array
	 */
	protected $namespaces = [];
	
	/**
	 * Statement list
	 * @var array
	 */
	protected $statements = [];
	
	/**
	 * Validates a given namespace ID
	 * @param string $namespaceId
	 * @throws \InvalidArgumentException
	 */
	protected function validateNamespaceId($namespaceId) {
		//validate $id
		if (!is_string($namespaceId) || !preg_match(SQLNamespace::NAMESPACE_ID_REGEX, $namespaceId)) {
			throw new \InvalidArgumentException("Namespace ID is not valid");
		}
	}
	
	/**
	 * Checks if the given namespace exists within this object
	 * @param string $namespaceId
	 * @return boolean
	 */
	public function hasNamespace($namespaceId) {
		return array_key_exists($namespaceId, $this->namespaces);
	}
	
	/**
	 * Adds a new namespace
	 * @param StatementNamespace $ns
	 */
	public function addNamespace(SQLNamespace $ns) {
		$this->namespaces[$ns->id] = $ns;
	}
	
	/**
	 * Obtains a namespace
	 * @param string $namespaceId
	 * @return StatementNamespace
	 */
	public function getNamespace($namespaceId) {
		if ($this->hasNamespace($namespaceId)) {
			return $this->namespaces[$namespaceId];
		}
		
		return false;
	}
	
	/**
	 * Check if the given statement has been declared
	 * @param string $statementId
	 */
	public function hasStatement($statementId) {
		if (!is_string($statementId) || empty($statementId)) {
			return false;
		}
		
		if (preg_match(SQLNamespace::INNER_NAMESPACE_REGEX, $statementId, $matches)) {
			$namespaceId = $matches[1];
		
			if (!$this->hasNamespace($namespaceId)) {
				return false;
			}
		
			return $this->namespaces[$namespaceId]->hasStatement($matches[2]);
		}
		
		return array_key_exists($statementId, $this->statements);
	}
	
	/**
	 * Adds a new statement
	 * @param Statement $stmt
	 */
	public function addStatement(Statement $stmt) {
		$this->statements[$stmt->id] = $stmt;
	}
	
	/**
	 * Obtains an statement
	 * @param string $id
	 * @return boolean|Statement
	 */
	public function getStatement($id) {
		if (!is_string($id) || empty($id)) {
			return false;
		}
	
		if (preg_match(SQLNamespace::INNER_NAMESPACE_REGEX, $id, $matches)) {
			$namespaceId = $matches[1];
	
			if (!$this->hasNamespace($namespaceId)) {
				return false;
			}
	
			return $this->namespaces[$namespaceId]->getStatement($matches[2]);
		}
	
		if (array_key_exists($id, $this->statements)) {
			return $this->statements[$id];
		}
	
		return false;
	}
	
	/*
	 * NAMESPACE CREATION METHODS
	 */
	
	/**
	 * Generates a new namespace and returns a reference
	 * @param string $namespaceId
	 * @return StatementNamespace
	 */
	public function &buildNamespace($namespaceId) {
		$this->validateNamespaceId($namespaceId);
		$this->namespaces[$namespaceId] = new SQLNamespace($namespaceId);
		return $this->namespaces[$namespaceId];
	}
	
	/**
	 * Obtains an inner namespace by reference
	 * If the requested namespace does not exists then a new one is generated
	 * @param string $namespaceId
	 * @return StatementNamespace
	 */
	public function &ns($namespaceId) {
		$this->validateNamespaceId($namespaceId);
	
		//check existence
		if (array_key_exists($namespaceId, $this->namespaces)) {
			return $this->namespaces[$namespaceId];
		}
	
		//create new namespace and return
		$this->namespaces[$namespaceId] = new SQLNamespace($namespaceId);
		return $this->namespaces[$namespaceId];
	}
	
	/*
	 * STATEMENT CREATION METHODS
	 */
	
	/**
	 * Generates a new empty statement within the namespace and returns a reference
	 * @param string $statementId
	 * @return Statement
	 */
	public function &buildStatement($statementId) {
		$this->validateStatementId($statementId);
		$this->statements[$statementId] = new Statement($statementId);
		return $this->statements[$statementId];
	}
	
	/**
	 * Generates an inner statement and returns the current namespace reference 
	 * @param string $statementId
	 * @param string $query
	 * @param StatementConfigurationContainer $config
	 * @return StatementNamespace
	 */
	public function &stmt($statementId, $query = '', StatementConfigurationContainer $config = null) {
		$this->validateStatementId($statementId);
		
		if (array_key_exists($statementId, $this->statements)) {
			return $this->statements[$statementId];
		}
		
		$this->statements[$statementId] = new Statement($statementId, $query, $config);
		return $this;
	}
}