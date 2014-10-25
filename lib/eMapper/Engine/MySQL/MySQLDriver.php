<?php
namespace eMapper\Engine\MySQL;

use eMapper\Engine\Generic\Driver;
use eMapper\Engine\MySQL\Type\MySQLTypeManager;
use eMapper\Engine\MySQL\Statement\MySQLStatement;
use eMapper\Engine\MySQL\Result\MySQLResultIterator;
use eMapper\Engine\MySQL\Exception\MySQLException;
use eMapper\Engine\MySQL\Exception\MySQLConnectionException;
use eMapper\Engine\MySQL\Exception\MySQLQueryException;
use eMapper\Engine\MySQL\Regex\MySQLRegex;

/**
 * The MySQLDriver class provides access to MySQL database engines.
 * @author emaphp
 */
class MySQLDriver extends Driver {
	public function __construct($database, $host = null, $user = null, $password = null, $port = null, $socket = null, $charset = 'UTF-8', $autocommit = true) {
		if ($database instanceof \mysqli)
			$this->connection = $database;
		else {
			if (empty($database))
				throw new \InvalidArgumentException("Invalid database specified");
				
			//initialize configuration
			$this->config['db.name'] = $database;
				
			if (isset($host)) {
				if (!is_string($host) || empty($host))
					throw new \InvalidArgumentException("Invalid host specified");
					
				$this->config['db.host'] = $host;
			}
				
			if (isset($user)) {
				if (!is_string($user) || empty($user))
					throw new \InvalidArgumentException("Invalid user specified");
					
				$this->config['db.user'] = $user;
			}
				
			if (isset($password)) {
				if (!is_string($password) || empty($password))
					throw new \InvalidArgumentException("Invalid password specified");
					
				$this->config['db.password'] = $password;
			}
				
			if (isset($port)) {
				if (!is_string($port) || !is_integer($port) || empty($port))
					throw new \InvalidArgumentException("Invalid port specified");
					
				$this->config['db.port'] = strval($port);
			}
				
			if (isset($socket)) {
				if (!is_string($socket) || empty($socket))
					throw new \InvalidArgumentException("Invalid socket specified");
					
				$this->config['db.socket'] = $socket;
			}
				
			if (isset($charset)) {
				if (!is_string($charset) || empty($charset))
					throw new \InvalidArgumentException("Invalid charset specified");
					
				$this->config['db.charset'] = $charset;
			}
				
			//aet autocommit option
			$this->config['db.autocommit'] = (bool) $autocommit;
			
			//build regex
			$this->regex = new MySQLRegex();
		}
	}
	
	public static function build($config) {
		if (!is_array($config))
			throw new \InvalidArgumentException("Static method 'build' expects an array as first argument");
		
		//validate database filename
		if (!array_key_exists('database', $config))
			throw new \InvalidArgumentException("Configuration value 'database' not found");
		
		$database = $config['database'];
		$host = array_key_exists('host', $config) ? $config['host'] : null;
		$username = array_key_exists('username', $config) ? $config['username'] : null;
		$password = array_key_exists('password', $config) ? $config['password'] : null;
		$port = array_key_exists('port', $config) ? $config['port'] : null;
		$socket = array_key_exists('socket', $config) ? $config['socket'] : null;
		$charset = array_key_exists('charset', $config) ? $config['charset'] : null;
		$autocommit = array_key_exists('autocommit', $config) ? $config['autocommit'] : null;
		
		return new static($database, $host, $username, $password, $port, $socket, $charset, $autocommit);
	}
	
	/*
	 * CONNECTION METHODS
	 */
	
	public function connect() {
		//check if connection is already opened
		if ($this->connection instanceof \mysqli)
			return $this->connection;
		
		//get connection values
		$database = $this->config['db.name'];
		$host     = array_key_exists('db.host', $this->config) ? $this->config['db.host'] : ini_get("mysqli.default_host");
		$user     = array_key_exists('db.user', $this->config) ? $this->config['db.user'] : ini_get("mysqli.default_user");
		$password = array_key_exists('db.password', $this->config) ? $this->config['db.password'] : ini_get("mysqli.default_pw");
		$port     = array_key_exists('db.port', $this->config) ? $this->config['db.port'] : ini_get("mysqli.default_port");
		$socket   = array_key_exists('db.socket', $this->config) ? $this->config['db.socket'] : ini_get("mysqli.default_socket");
		
		//open connection
		$mysqli = @mysqli_connect($host, $user, $password, $database, $port, $socket);
		
		if (!($mysqli instanceof \mysqli))
			throw new MySQLConnectionException(mysqli_connect_error() . '(' . mysqli_connect_errno() . ')');
		
		//set autocommit
		$mysqli->autocommit($this->config['db.autocommit']);
		
		//set charset
		if (array_key_exists('db.charset', $this->config))
			$mysqli->set_charset($this->config['db.charset']);
		
		//store open connection
		return $this->connection = $mysqli;
	}
	
	public function query($query) {
		return $this->connection->query($query);
	}
	
	public function freeResult($result) {
		//free result
		if ($result instanceof \mysqli_result)
			$result->free();
		
		//free additional results
		while ($this->connection->more_results() && $this->connection->next_result()) {
			$result = $this->connection->use_result();
		
			if ($result instanceof \mysqli_result)
				$result->free();
		}
	}
	
	public function close() {
		if ($this->connection instanceof \mysqli)
			return $this->connection->close();
	}
	
	public function getLastError() {
		if (!($this->connection instanceof \mysqli))
			throw new MySQLException("No valid MySQL connection available");
	
		return mysqli_error($this->connection);
	}
	
	public function getLastId() {
		if (!($this->connection instanceof \mysqli))
			throw new MySQLException("No valid MySQL connection available");
	
		return $this->connection->insert_id;
	}
	
	/*
	 * TRANSACTION METHODS
	 */
	
	public function begin() {
		if (!($this->connection instanceof \mysqli))
			throw new MySQLException("No valid MySQL connection available");
	
		if (version_compare(PHP_VERSION, '5.5.0') >= 0)
			return $this->connection->begin_transaction();
		
		return $this->connection->query("START TRANSACTION");
	}
	
	public function commit() {
		if (!($this->connection instanceof \mysqli))
			throw new MySQLException("No valid MySQL connection available");
	
		return $this->connection->commit();
	}
	
	public function rollback() {
		if (!($this->connection instanceof \mysqli))
			throw new MySQLException("No valid MySQL connection available");
	
		return $this->connection->rollback();
	}
	
	/*
	 * BUILDER METHODS
	 */
	
	public function buildTypeManager() {
		return new MySQLTypeManager();
	}
	
	public function buildStatement($typeManager) {
		return new MySQLStatement($this, $typeManager);
	}
	
	public function buildResultIterator($result) {
		return new MySQLResultIterator($result);
	}
	
	public function buildCall($procedure, $tokens, $config) {
		return "CALL $procedure(" . implode(',', $tokens) . ')';
	}
	
	/*
	 * EXCEPTION METHODS
	 */
	
	public function throwException($message, \Exception $previous = null) {
		throw new MySQLException($message, null, $previous);
	}
	
	public function throwQueryException($query) {
		throw new MySQLQueryException(mysqli_error($this->connection), $query);
	}
}
?>