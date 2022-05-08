<?php

class Renderer {
	protected $templatePath;
	private $attributes;

	/**
	 * Renderer constructor.
	 * @param $templatePath
	 * @param $attributes
	 */
	public function __construct($templatePath = "", $attributes = []) {
		$this->templatePath = rtrim($templatePath, '/\\') . '/';
		$this->attributes = $attributes;
	}

	public function getTemplatePath() {
		return $this->templatePath;
	}
	public function setTemplatePath($templatePath) {
		$this->templatePath = $templatePath;
		if ('/' != mb_substr($this->templatePath, -1)) {
			$this->templatePath .= '/';
		}
		return $this;
	}

	public function render(string $templateName, array $data = []) {
		if (!file_exists($this->templatePath . $templateName)) {
			throw new \RuntimeException("Template $templateName does not exists in path ".$this->templatePath, 51);
		}

		try {
			$data = array_merge($this->attributes, $data);

			ob_start();
			$this->sandbox($this->templatePath . $templateName, $data);
		} catch (\Throwable $exc) {
			ob_get_clean();
			error_log($exc->getMessage()."\n".$exc->getTraceAsString());
			Log::p($exc->getMessage()."\n".$exc->getTraceAsString());
		}
	}

	private function sandbox(string $template, array $data) {
		extract($data);
		include func_get_arg(0);
	}
}