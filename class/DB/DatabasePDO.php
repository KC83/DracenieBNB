<?php
require "class/DB/Log.php";

class DatabasePDO extends \PDO {
	protected static $_instance;
	protected $log;

	public function __construct(string $dsn, string $username = null, string $passwd = null, array $options = null) {
		parent::__construct($dsn, $username, $passwd, $options);
		$this->log = new Log("logs", "LogPHP");
		return $this;
	}

	public static function getInstance(): DatabasePDO {
		if (self::$_instance) {
			return self::$_instance;
		}

		$config = include_once "inc/connect.php";
		if ($config) {
			$dsn = 'mysql:host=' . $config['host'] . ';dbname=' . $config['database'] . ';charset=utf8';
			self::$_instance = new DatabasePDO($dsn, $config['login'] ?? null, $config['password'] ?? null, $config['options'] ?? null);
		} else {
			if (is_null(self::$_instance)) {
				throw new \RuntimeException('Instanciez la connexion à la bdd.');
			}
		}

		return self::$_instance;
	}

	public function queryData(string $sql, ?array $args = [], bool $debug = false, $table = null) {
		if (is_array($args)) {
			foreach ($args as $key => $value) {
				if (is_array($value)) {
					// IN case
					$in = array_values($value);
					$sql = str_replace($key, "'" . implode("','", $in) . "'", $sql);
					unset($args[$key]);
				}
			}
		}

		// bind params
		$stmt = $this->prepare($sql);

		if (!$stmt) {
			return $this->ExceptionLog($this->errorInfo()[2]);
		}
		if (is_array($args)) {
			foreach ($args as $key => $value) {
				if (is_int($value)) {
					$stmt->bindValue($key, $value, \PDO::PARAM_INT);
				} elseif (is_null($value)) {
					$stmt->bindValue($key, null, \PDO::PARAM_INT);
				} else {
					$stmt->bindValue($key, $value, \PDO::PARAM_STR);
				}
			}
		}

		try {
			$stmt->execute();
		} catch (\Throwable $ex) {
			return $this->ExceptionLog($ex->getMessage());
		}

		if ($debug) {
			$indexed = ($args == array_values($args));
			foreach ($args as $k => $v) {
				if (is_string($v)) {
					$v = "'{$v}'";
				}
				if ($indexed) {
					$sql = preg_replace('/\?/', $v, $sql, 1);
				} else {
					if (is_array($v)) {
						$sql = str_replace($k, "'" . implode("','", $v) . "'", $sql);
					} else {
						$sql = str_replace($k, $v, $sql);
					}
				}
			}

			echo $sql;
		}

		return $stmt;
	}

	public function replace(string $table, string $primaryKey, array $data, int $id = null, ?bool $debug = false): ?int {
		if (empty($id) && !empty($data[$primaryKey])) {
			$id = $data[$primaryKey];
			unset($data[$primaryKey]);
		} elseif (empty($id)) {
			$id = null;
			unset($data[$primaryKey]);
		}

		$vars = [];
		foreach ($data as $key => $value) {
			if ('' === $value) {
				$value = null;
			}

			if (false === $value) {
				$value = 0;
			} elseif (true === $value) {
				$value = 1;
			}

			if (!empty($value) && is_string($value) && preg_match('!^(0?\d|[12]\d|3[01])/(0?\d|1[012])/((?:19|20|21|22|23|24|25)\d{2})$!', $value)) {
				$dt = new \DateTime(str_replace('/', '-', $value));
				$value = $dt->format('Y-m-d');
			}
			$vars[':' . $key] = $value;
		}

		if ($id) {
			$sql = 'SELECT * FROM ' . $table . ' WHERE ' . $primaryKey . ' = :id ';
			$res = $this->queryData($sql, [':id' => $id], false, $table, $primaryKey, $id);
			if (($row = $res->fetchObject())) {
				$pack_fields = [];

				foreach ($data as $key => $value) {
					$pack_fields[] = '`' . $key . '` = :' . $key;
				}

				$sql = 'UPDATE ' . $table . ' SET  ' . implode(',', $pack_fields) . ' WHERE ' . $primaryKey . ' = :' . $primaryKey . '';
				$vars[':' . $primaryKey] = $id;
				$this->queryData($sql, $vars, $debug, $table, $primaryKey, $id);
			}
		} else {
			$pack_values = [];
			$champs = [];
			foreach ($data as $key => $value) {
				$pack_values[] = ':' . $key;
				$champs[] = $key;
			}

			$sql = 'INSERT INTO ' . $table . ' (' . implode(',', $champs) . ') VALUES (' . implode(',', $pack_values) . ')';
			$this->queryData($sql, $vars, $debug, $table, $primaryKey, $id);
		}

		if ($id) {
			return intval($id);
		}

		return $this->lastInsertId();
	}

	public function ExceptionLog($message , $sql = ""): PDOException {
		$exception  = 'Unhandled Exception. <br />';
		$exception .= $message;
		$exception .= "<br /> You can find the error back in the log.";

		if(!empty($sql)) {
			# Add the Raw SQL to the Log
			$exception .= "\r\nRaw SQL : "  . $sql;
		}
		# Write into log
		$this->log->write($exception);
		throw new PDOException($exception);
	}
}