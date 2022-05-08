<?php
require "class/DB/DatabasePDO.php";

class DB {
	public static function getPdo(): DatabasePDO {
		return DatabasePDO::getInstance();
	}

	public static function query($sql, $vars = null, $debug = false) {
		$databasePDO = self::getPdo();
		return $databasePDO->queryData($sql, $vars, $debug);
	}

	public static function updateDefault($id, string $table, string $name_id, ?array $cle_tab=[], ?bool $debug = false): int {
		$evodePdo = self::getPdo();
		return intval($evodePdo->replace($table, $name_id, $cle_tab, intval($id), $debug));
	}

	public static function num_rows($req) {
		return $req->rowCount();
	}
}