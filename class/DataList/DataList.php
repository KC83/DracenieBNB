<?php

class DataList {
	protected $sql;
	protected $pdoBindings;

	protected $id;

	protected $orderBy;
	protected $limitStart;
	protected $limitEnd;
	protected $debug;

	protected $columnKey;
	protected $columnKeyType;
	protected $columnValue;

	protected $tableCss;
	protected $tableTrCss;
	protected $tableThCss;
	protected $tableTdCss;

	protected $onClick;
	protected $onClickParams;

	const TYPE_STRING = 0;
	const TYPE_BOOL = 1;
	const TYPE_DATE = 2;

	public function __construct($sql) {
		$this->sql = $sql;

		$this->columnKey = [];
		$this->columnKeyType = [];
		$this->columnValue = [];

		$this->debug = false;
	}

	public function setColumn(string $columnName, string $columnDisplayName, int $columnType = self::TYPE_STRING) {
		$this->columnKey[$columnName] = $columnDisplayName;
		$this->columnKeyType[$columnName] = $columnType;
	}
	public function show() {
		self::initDatas();

		$renderer = new Renderer();
		$renderer->setTemplatePath(__DIR__.'/Views/');

		$renderAttributes = [
			  "dataList" => $this
		];
		$renderer->render('DataListView.php', $renderAttributes);
	}

	//### PROTECTED FUNCTION ###//

	/**
	 * Exécute le SQL et remplis les tableaux avec les données
	 */
	protected function initDatas() {
		$query = DB::query($this->getSql(), $this->getPdoBindings(), $this->getDebug());
		$results = $query->fetchAll();

		if (count($results) > 0) {
			foreach ($results as $idx=> $rows) {
				foreach ($rows as $col => $val) {
					if (is_int($col)) {
						continue;
					}

					$colType = $this->columnKeyType[$col] ?? self::TYPE_STRING;
					switch ($colType) {
						case self::TYPE_BOOL:
							if ($val == 1) {
								$val = '<i class="fa fa-check-circle text-success"></i>';
							} else if ($val == 0) {
								$val = '<i class="fa fa-times-circle text-danger"></i>';
							}

							break;
						case self::TYPE_DATE:
							try {
								$date = new DateTime($val);
								$val = $date->format('d/m/Y');
							} catch (Exception $e) {
							}

							break;
					}

					$this->columnValue[$idx][$col] = $val;
				}
			}
		}
	}

	//###### GETTERS && SETTERS ######//

	public function getSql() {
		return $this->sql . ' ' . $this->getOrderBySql() . ' ' . $this->getLimitSql();
	}

	// Paramètres des requêtes
	public function getPdoBindings() {
		return $this->pdoBindings ?? [];
	}
	public function setPdoBindings($pdoBindings): void {
		$this->pdoBindings = $pdoBindings;
	}

	public function getOrderBySql(): string {
		if (!is_array($this->orderBy) || (is_array($this->orderBy) && count($this->orderBy) == 0)) {
			return '';
		}

		$sql = 'ORDER BY ';
		$cpt = 0;
		foreach ($this->orderBy as $key => $value) {
			if ($cpt > 0) {
				$sql .= ", ";
			}
			$sql .= "$key $value ";
			$cpt++;
		}

		return $sql;
	}
	public function setOrderBy(?array $orderByList) {
		$this->orderBy = [];

		foreach ($orderByList as $key => $value) {
			if (empty($key) || ($value != 'ASC' && $value != 'DESC')) {
				continue;
			}
			$this->orderBy[$key] = $value;
		}

		return $this;
	}

	public function getLimitSql(): string {
		if ($this->limitEnd) {
			return " LIMIT " . $this->limitStart . ',' . $this->limitEnd . " ";
		}
		return '';
	}
	public function setLimitSql(int $start = 0, ?int $end = null) {
		$this->limitStart = $start;
		$this->limitEnd = $end;
	}

	// Colonnes
	public function getColumnKey() {
		return $this->columnKey ?? [];
	}
	public function getColumnValue() {
		return $this->columnValue ?? [];
	}

	public function getTableCss() {
		return $this->tableCss;
	}
	public function setTableCss($tableCss): void {
		$this->tableCss = $tableCss;
	}

	public function getTableTrCss() {
		return $this->tableTrCss;
	}
	public function setTableTrCss($tableTrCss): void {
		$this->tableTrCss = $tableTrCss;
	}

	public function getTableTdCss() {
		return $this->tableTdCss;
	}
	public function setTableTdCss($tableTdCss): void {
		$this->tableTdCss = $tableTdCss;
	}

	public function getOnClick() {
		return $this->onClick;
	}
	public function getOnClickRow(array $data) {
		$params = '';
		if (!empty($this->getOnClickParams())) {
			$onClickParams = explode(',',$this->getOnClickParams());

			foreach ($onClickParams as $param) {
				if(!empty($params)) {
					$params .= ',';
				}
				$params .= "'".$data[$param]."'";
			}
		}

		return $this->getOnClick().'('.$params.')';
	}
	public function setOnClick($onClick): void {
		$this->onClick = $onClick;
	}

	public function getOnClickParams() {
		return $this->onClickParams;
	}
	public function setOnClickParams($onClickParams): void {
		$this->onClickParams = $onClickParams;
	}

	public function getId() {
		return $this->id;
	}
	public function setId($id): void {
		$this->id = $id;
	}

	public function getDebug() {
		return $this->debug;
	}
	public function setDebug($debug): void {
		$this->debug = $debug;
	}
}