<?php
/*
	ELSWAK File Response
	
	This class handles responses which read files on the local filesystem to the response.
*/

class ELSWAK_File_Response
	extends ELSWAK_HTTP_Response {
	protected $fileName;
	protected $filePath;
	
	public function __construct($filePath = null, $fileName = null, $download = true) {
		parent::__construct();
		
		$this->setHeader('Content-Transfer-Encoding', 'binary', true);
		
		if ($filePath !== null)
			$this->setFile($filePath);
		
		if ($fileName !== null)
			$this->setFileName($fileName);
		
		if ($download)
			$this->setDownload();
		else
			$this->setInline();
	}
	public function setInline() {
		$this->setHeader('Content-Disposition', 'inline', true);
		return $this;
	}
	public function setDownload() {
		if ($this->fileName != false)
			$this->setHeader('Content-Disposition', 'attachment; filename='.$this->fileName, true);
		else
			$this->setHeader('Content-Disposition', 'attachment', true);
		return $this;
	}
	public function setFile($filePath) {
		if (is_readable($filePath)) {
			$this->filePath = $filePath;
			$this->setHeader('Content-Type', ELSWAK_File_Type_Detector::typeFromName($this->filePath), true);
			$this->setHeader('Content-Length', filesize($this->filePath), true);
		} else
			throw new Exception('Unable to set file path. Please specify a valid file on the local file system.');

	}
	public function setFileName($fileName = null) {
		if (is_string($fileName))
			$this->fileName = $fileName;
		else if (is_readable($this->filePath))
			$this->fileName = basename($this->filePath);
		else
			$this->fileName = false;
		
		return $this->setDownload();
	}
	public function addContent($content) {
		return $this->setFile($content);
	}
	public function addMessage($message) {
		return $this;
	}
	public function content() {
		if (is_readable($this->filePath))
			return file_get_contents($this->filePath);
		return false;
	}
	public function sendContent() {
		if (is_readable($this->filePath)) {
			$outputBytes = readfile($this->filePath);
		}
		return $this;
	}
}