<?php

class Log {
	private $dirName;
	private $fileName;

	public function __construct($dirName, $fileName) {
		date_default_timezone_set('Europe/Paris');
		$this->dirName = $dirName;
		$this->fileName = $fileName;
	}

	public function write($message) {
		$date = new DateTime();
		$path = $this->dirName."/".$this->fileName." ".$date->format('Y-m-d').".txt";

		if (!is_dir($this->dirName)) {
			mkdir($this->dirName, 0777) ;
		}

		$fh  = fopen($path, 'a+') or die("Fatal Error !");
		$logcontent = "Time : " . $date->format('H:i:s')."\r\n" . $message ."\r\n";
		fwrite($fh, $logcontent);
		fclose($fh);
	}

	public static function p($message) {
		echo "<pre>";
		print_r($message);
		echo "</pre>";
	}

	public static function alert(string $message, ?string $alertClass = "alert-danger") {
		echo '<div class="alert '.$alertClass.'">'.$message.'</div>';
	}
}